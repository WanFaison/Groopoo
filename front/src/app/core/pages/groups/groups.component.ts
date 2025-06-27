import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { GroupeModel, GroupeReqModel } from '../../models/groupe.model';
import { GroupeServiceImpl } from '../../services/impl/groupe.service.impl';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { ListeModel } from '../../models/liste.model';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { EtudiantCreate, EtudiantCreateXlsx, EtudiantModel } from '../../models/etudiant.model';
import { EtudiantServiceImpl } from '../../services/impl/etudiant.service.impl';
import { HttpResponse } from '@angular/common/http';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { ClasseModel } from '../../models/classe.model';
import { ClasseServiceImpl } from '../../services/impl/classe.service.impl';
import { PaginatorService } from '../../services/pagination.service';

@Component({
    selector: 'app-groups',
    imports: [CommonModule, FormsModule, ReactiveFormsModule],
    templateUrl: './groups.component.html',
    styleUrl: './groups.component.css'
})
export class GroupsComponent implements OnInit{
  etdForm:FormGroup;
  liste: number = 0;
  listeResponse?: RestResponse<ListeModel>;
  etdResponse?: RestResponse<EtudiantCreateXlsx[]>;
  etdResponse2?: RestResponse<EtudiantModel>;
  groupResponse?: RestResponse<GroupeModel[]>;
  grpReq?:RestResponse<GroupeReqModel[]>
  classeResponse?:RestResponse<ClasseModel[]>
  grp:number = 0;
  user?:LogUser;
  libelle:string = ''
  error:boolean = false;
  coachs:Array<number> = [];
  msg: string='';
  ajout: any;
  constructor(private router:Router, private paginatorService:PaginatorService, private fb:FormBuilder, private classeService:ClasseServiceImpl, private authService:AuthServiceImpl, private groupeService:GroupeServiceImpl, private listeService:ListeServiceImpl, private apiService:ApiService, private etudiantService:EtudiantServiceImpl) 
  {
    this.etdForm = this.fb.group({
      nom: ['', Validators.required],
      prenom: ['', Validators.required],
      matricule: ['', Validators.required],
      classe: ['', Validators.required],
      sexe: true
    })
  }

  get nomControl() {
    return this.etdForm.get('nom');
  }
  get prenomControl() {
    return this.etdForm.get('prenom');
  }
  get matriculeControl() {
    return this.etdForm.get('matricule');
  }
  get classeId() {
    return this.etdForm.get('classe')?.value;
  }

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
      this.groupeService.findAllReq(this.liste).subscribe(data=>this.grpReq=data)
    }
    this.refresh(this.liste);

    this.etdForm.valueChanges.subscribe(value => {
      console.log(this.etdForm.value)
    });
  }

  findEtd(id:number){
    this.etudiantService.findById(id).subscribe(data=>this.etdResponse2=data);
  }
  findClasses(group:number) {
    this.grp = group;
    this.classeService.findAllByEcoleOrListe(this.liste).subscribe(data=>this.classeResponse=data);
  }
  
  checkCorrect() {
    if(this.classeId == 0){
      this.error = true
      this.setMsg('Choissisez une classe!')
    }else{
      this.addEtdToGrp()
    }
  }

  addEtdToGrp(){
    this.etudiantService.addEtudiantToListe(this.etdForm.value, this.grp)
    .subscribe((response:RequestResponse) =>{
            console.log('Response from back-end:', response);
            if(response.data != 0){
              this.error = true;
              this.setMsg('Cette entité existe deja dans cette organisation')
            }else{
              this.ajout = true;
            }
          }, error => {
            console.error('Error:', error);
          });
  }

  transferEtd(grp:number = this.grp){
    if(this.etdResponse2)
    this.etudiantService.transferEtudiant(this.etdResponse2?.results.id, grp).subscribe(
      response=>{
        if(response.data != 0){
          this.error = true;
        }else{
          this.reloadPage(); 
        } 
      },        
      error => {
            console.error('Error sending data', error);
          });
  }

  deleteEtd() {
    if(this.etdResponse2)
    this.etudiantService.deleteEtudiant(this.etdResponse2?.results.id).subscribe(
      response=>{
        if(response.data != 0){
          console.log(response.message)
        }else{
          this.reloadPage(); 
        } 
      },        
      error => {
            console.error('Error sending data', error);
          }
    );
  }

  printXls(motif:string = ''){
    this.apiService.getExcelSheet(this.liste, motif).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      if(motif == 'results'){
        link.download = `${this.listeResponse?.results.libelle} Resultats.xlsx`;
      }else{
        link.download = `${this.listeResponse?.results.libelle}.xlsx`;
      }
      link.click();
    });
  }

  printSalleXls(motif:string = ''){
    this.groupeService.getSalleSheet(this.liste, motif).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = `${this.listeResponse?.results.libelle} - Repartition des Coachs.xlsx`;
      link.click();
    });
  }

  printPdf(){
    this.apiService.getPdf(this.liste, { observe: 'response', responseType: 'blob' }).subscribe(
      (response: HttpResponse<Blob>) => {
        const blob = new Blob([response.body!], { type: 'application/pdf' });
        const libelle = response.headers.get('X-Liste-Libelle') || 'Nouvelle Liste';
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${libelle}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
      },
      error => {
        console.error('Error exporting PDF:', error);
        if (error.status === 0) {
          console.error('Network or CORS issue.');
        }
      }
    );
  }

  useListe(){
    this.etudiantService.findByListe(this.liste).subscribe(data=>this.etdResponse=data);
    console.log(this.etdResponse);

    if(this.listeResponse){
      localStorage.setItem('formData', JSON.stringify(this.listeResponse?.results.critere))
    }

    if(this.etdResponse){
      localStorage.setItem('etudiants', JSON.stringify(this.etdResponse.results));
      this.clearData();
      localStorage.setItem('homeMenu', '2');
      this.router.navigate(['/app/home']);
    }
    
  }

  reDoList(){
    this.listeService.reDoListe(this.liste).subscribe(
      response=>{
                if (typeof window !== 'undefined' && localStorage){
                  localStorage.setItem('newListe', response.data)
                } 
                this.reloadPage();
              },        
      error => {
                console.error('Error sending data', error);
              });
    console.log('reformer liste')
  }

  modifEnt(ent: any,lib: string) {
    this.listeService.modifListe(ent, 'modif', lib).subscribe(
      response=>{
        if(response.data != 0){
          this.error = true;
        }else{
          this.reloadPage(); 
        } 
      },        
      error => {
            console.error('Error sending data', error);
          });
  }

  setMsg(msg:string = ''){
    this.msg = msg;
  }

  refresh(liste:number=0, page:number=0, limit:number=10){
    this.groupeService.findAll(liste, page, limit).subscribe(data=>this.groupResponse=data);
  }
  paginate(page:number){
    this.refresh(this.liste, page)
  }
  filter(index:number) {
    this.refresh(this.liste, index, 1)
  }

  getPageRange(currentPage:any, totalPages:any): number[] {
    return this.paginatorService.getPageRange(currentPage, totalPages)
  }

  reloadPage() {
    return this.paginatorService.reloadPage();
  }

  clearData(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('tailleGrp')
      localStorage.removeItem('nomGrp');
      localStorage.removeItem('anneeListe');
    }
    
  }

}
