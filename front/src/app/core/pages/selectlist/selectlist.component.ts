import { Component, OnInit } from '@angular/core';
import { NavComponent } from '../../components/nav/nav.component';
import { FootComponent } from '../../components/foot/foot.component';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { Router, RouterLink } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { LogUser } from '../../models/user.model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { EtudiantCreateXlsx, EtudiantImportXlsx } from '../../models/etudiant.model';
import * as XLSX from 'xlsx';
import { AnneeModel } from '../../models/annee.model';
import { EcoleModel } from '../../models/ecole.model';
import { RestResponse } from '../../models/rest.response';
import { ApiService } from '../../services/api.service';
import { AnneeServiceImpl } from '../../services/impl/annee.service.impl';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';


@Component({
  selector: 'app-selectlist',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, CommonModule, RouterLink],
  templateUrl: './selectlist.component.html',
  styleUrl: './selectlist.component.css'
})
export class SelectlistComponent implements OnInit{
  ecoleResponse?: RestResponse<EcoleModel[]>;
  anneeResponse?: RestResponse<AnneeModel[]>;
  etudiantGroups: { groupName: string, groupNote: number, etudiants: EtudiantImportXlsx[] }[] = [];
  fileName: any = '';
  annee: number = 0;
  ecole: number = 0;
  error:boolean = false;
  user?:LogUser
  constructor(private router:Router, private authService:AuthServiceImpl, private listeService:ListeServiceImpl, private ecoleService:EcoleServiceImpl, private apiService: ApiService, private anneeService:AnneeServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/not-found'])
    }

    this.loadProps()
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    this.anneeService.findAll().subscribe(data=>this.anneeResponse=data);
  }

  saveEcole(value: any) {
    localStorage.setItem('ecoleListe', value);
  }
  saveAnnee(value: any) {
    localStorage.setItem('anneeListe', value);
    //this.loadProps()
  }
  loadProps(){
    if (typeof window !== 'undefined' && localStorage){
      //this.fileName = localStorage.getItem('nomGrp');
      this.annee = parseInt(localStorage.getItem('anneeListe') || '0', 10);
      this.ecole = parseInt(localStorage.getItem('ecoleListe') || '0', 10);
    }
  }

  printTemplate(){
    this.listeService.getTemplate(1).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = 'Band-It import template.xlsx';
      link.click();
    });
  }

  onFileSelected(event: any): void {
      const file = event.target.files[0];

      if(file){
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        if (!allowedTypes.includes(file.type)) {
          alert('Please upload a valid Excel file.');
          return;
        }
        this.fileName = file.name;

        const reader = new FileReader();
        reader.onload = (e: any) => {
          const workbook = XLSX.read(e.target.result, { type: 'binary' });
          const sheetName = workbook.SheetNames[0];
          const sheet = workbook.Sheets[sheetName];
          const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

          let currentGroup: { groupName: string, groupNote: number, etudiants: EtudiantImportXlsx[] } = { groupName: '', groupNote: 0, etudiants: [] };

          rows.forEach((row: any) => {
            if (this.isGroupHeader(row)) { 
              if (currentGroup.groupName) {
                this.etudiantGroups.push(currentGroup);
              }
              currentGroup = { groupName: row[0]?.trim(), groupNote: row[1] ?? 0, etudiants: [] };
            } else if (row[0] && row[1]) {  
              const etdActu:EtudiantImportXlsx = {
                matricule: row[0]?.trim(),
                nom: row[1],
                prenom: row[2],
                sexe: row[3]?.trim(),
                classe: row[4]?.trim(),
                niveau: row[5]?.trim(),
                filiere: row[6]?.trim()
              };
              currentGroup.etudiants.push(etdActu);
            }
          });

          if (currentGroup.groupName) {
            this.etudiantGroups.push(currentGroup);
          }

          console.log(this.etudiantGroups);
        };

        reader.readAsBinaryString(file);
      }    
  }

  private isGroupHeader(row: any[]): boolean {
    return typeof row[0] === 'string' && row[0].includes('Groupe');
  }

  onSubmit() {
    this.loadProps();
    console.log(this.ecole, this.annee, this.etudiantGroups)
    const data = {
      ecole: this.ecole,
      annee: this.annee,
      fileName: this.fileName.replace(/\.[^/.]+$/, ""),
      etudiantGroups: this.etudiantGroups
    }

    this.listeService.importList(data).subscribe(
      response =>{
        console.log(response)
        alert(`La liste a été enregistrée`)
        this.router.navigate(['/app/home']);
      },
      error => {
        console.error('Error sending data', error);
        this.error = true
      }
    )
  }

  reloadPage() {
    window.location.reload();
  }
}
