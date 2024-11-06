import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../../components/nav/nav.component";
import { FootComponent } from "../../components/foot/foot.component";
import * as XLSX from 'xlsx';
import { EtudiantCreate, EtudiantCreateXlsx, EtudiantModel } from '../../models/etudiant.model';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { EcoleModel } from '../../models/ecole.model';
import { RestResponse } from '../../models/rest.response';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { NiveauModel } from '../../models/niveau.model';
import { ClasseModel } from '../../models/classe.model';
import { FiliereModel } from '../../models/filiere.model';
import { ApiService } from '../../services/api.service';
import { ReturnResponse } from '../../models/return.model';
import { AnneeModel } from '../../models/annee.model';
import { AnneeServiceImpl } from '../../services/impl/annee.service.impl';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { count } from 'node:console';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';


@Component({
  selector: 'app-membres',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, CommonModule, RouterLink],
  templateUrl: './membres.component.html',
  styleUrl: './membres.component.css'
})
export class MembresComponent implements OnInit{
  ecoleResponse?: RestResponse<EcoleModel[]>;
  fileName: any = '';
  etudiants: EtudiantCreateXlsx[] = [];
  criteres: any[] = [];
  critCheck: boolean = false;
  anneeResponse?: RestResponse<AnneeModel[]>;
  annee: number = 0;
  ecole: number = 0;
  tailleGrp: number = 0;
  error: boolean = false;
  returnResponse?: ReturnResponse;
  user?:LogUser
  constructor(private router:Router, private listeService:ListeServiceImpl, private authService:AuthServiceImpl, private ecoleService:EcoleServiceImpl, private apiService: ApiService, private anneeService:AnneeServiceImpl) { }

  ngOnInit(): void {
    //this.clearData()
    this.user = this.authService.getUser()
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/not-found'])
    }

    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    this.anneeService.findAll().subscribe(data=>this.anneeResponse=data);
    this.loadEtudiants();
    this.loadCriteres();
    this.loadProps();
  }

  printTemplate(){
    this.listeService.getTemplate().subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = 'Band-It template.xlsx';
      link.click();
    });
  }

  reloadPage() {
    window.location.reload();
  }

  saveEcole(value: any) {
    localStorage.setItem('ecoleListe', value);
  }
  saveTaille(value: any) {
    localStorage.setItem('tailleGrp', value);
    this.loadProps()
  }
  saveNom(value: any) {
    localStorage.setItem('nomGrp', value);
    this.loadProps()
  }
  saveAnnee(value: any) {
    localStorage.setItem('anneeListe', value);
    this.loadProps()
  }

  loadProps(){
    if (typeof window !== 'undefined' && localStorage){
      this.fileName = localStorage.getItem('nomGrp');
      this.tailleGrp = parseInt(localStorage.getItem('tailleGrp') || '0', 10);
      this.annee = parseInt(localStorage.getItem('anneeListe') || '0', 10);
      this.ecole = parseInt(localStorage.getItem('ecoleListe') || '0', 10);
    }
    this.critCheck = (typeof window !== 'undefined' && !!localStorage.getItem('formData') && !!localStorage.getItem('etudiants'));
  }

  loadEtudiants(){
    if (typeof window !== 'undefined' && localStorage){
      const existingData = localStorage.getItem('etudiants');
      if (existingData) {
        this.etudiants = JSON.parse(existingData);
      }else {
        console.error('localStorage is not available in this environment');
      }
    }
    
  }

  importExcel(event: any): void {
    const file = event.target.files[0];
    if (file) {
      const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
      if (!allowedTypes.includes(file.type)) {
        alert('Please upload a valid Excel file.');
        return;
      }
      this.fileName = file.name;

      const reader = new FileReader();
      reader.onload = (e: any) => {
        const arrayBuffer = e.target.result;
        const workbook = XLSX.read(arrayBuffer, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const json = XLSX.utils.sheet_to_json<EtudiantCreateXlsx>(workbook.Sheets[sheetName]);

        this.loadEtudiants();

        this.etudiants = [...this.etudiants, ...json];
        localStorage.setItem('etudiants', JSON.stringify(this.etudiants));
        console.log(this.etudiants);
      };
      reader.readAsArrayBuffer(file);
    }
  }

  deleteEtd(index:number){
    this.etudiants.splice(index, 1);
    localStorage.setItem('etudiants', JSON.stringify(this.etudiants));
  }

  loadCriteres(){
    if (typeof window !== 'undefined' && localStorage.getItem('formData')){
      const storedData = localStorage.getItem('formData');
    
      if (storedData) {
        const parsedData = JSON.parse(storedData);
        if (parsedData && Object.keys(parsedData).length > 0){
          this.criteres = [
            ...parsedData.niveau.map((item: any) => ({ choix: item.choix, taille: item.taille })),
            ...parsedData.filiere.map((item: any) => ({ choix: item.choix, taille: item.taille })),
            ...parsedData.classe.map((item: any) => ({ choix: item.choix, taille: item.taille }))
          ];
          
          console.log(this.criteres);
        }
      }else {
        console.error('No data found in localStorage for the specified key.');
      }
    }
    
  }

  checkCorrect():boolean{
    const ecoleNum = parseInt(localStorage.getItem('ecoleListe') || '0', 10);
    const anneeListe = parseInt(localStorage.getItem('anneeListe') || '0', 10);
    const taille = parseInt(localStorage.getItem('tailleGrp') || '0', 10);
    const etds = localStorage.getItem('etudiants');
    const nom = localStorage.getItem('nomGrp');

    if ((ecoleNum ==0) || (taille < 2) || (nom === '') || (anneeListe ==0) ||
        (!etds || etds === 'null' || etds === 'undefined' || etds === '')) {
      console.log('Error: formData, etudiants, ecole or taille');
      return false;
    } else {
      const etdData = JSON.parse(etds);
      
      if ((etdData.length < 1)) {
        console.log('criteres or etudiant list is empty');
        return false;
      }
    }

    return true;
  }

  clearData(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('formData');
      localStorage.removeItem('tailleGrp')
      localStorage.removeItem('etudiants')
      localStorage.removeItem('nomGrp');
      localStorage.removeItem('anneeListe');
    }
    
  }

  clearEtd(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('etudiants')
      this.reloadPage();
    }
  }

  createGroups(){
    if(this.checkCorrect()){
      if (typeof window !== 'undefined' && localStorage){
        const formData = localStorage.getItem('formData');
        const ecoleNum = parseInt(localStorage.getItem('ecoleListe') || '0', 10);
        const taille = parseInt(localStorage.getItem('tailleGrp') || '0', 10);
        const anneeListe = parseInt(localStorage.getItem('anneeListe') || '0', 10);
        const etds = localStorage.getItem('etudiants');
        const nom = localStorage.getItem('nomGrp');

        this.returnResponse = {
          ecole: ecoleNum,
          annee: anneeListe,
          taille: taille, 
          nom: nom? nom:'',  
          etudiants: etds? JSON.parse(etds):[],  
          criteres: formData? JSON.parse(formData) : [],  
          status: 200
        };
  
        alert("Ce processus peut prendre plus de temps que prévu en fonction du nombre d'étudiants. Veuillez patienter.")
        this.apiService.sendDataToBackend(this.returnResponse).subscribe(
          response => {
            console.log('Data successfully sent', response);
            this.clearData();
            localStorage.setItem('newListe', response.data);
            this.router.navigate(['/app/view-groups']);
          },
          error => {
            console.error('Error sending data', error);
            this.error = true;
          }
        );
      }else{
        this.error = true;
      }
    }else{
      this.error = true;
    } 
  }

}
