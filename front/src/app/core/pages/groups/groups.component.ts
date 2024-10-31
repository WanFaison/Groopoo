import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { RestResponse } from '../../models/rest.response';
import { GroupeModel } from '../../models/groupe.model';
import { GroupeServiceImpl } from '../../services/impl/groupe.service.impl';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { ListeModel } from '../../models/liste.model';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { EtudiantCreate, EtudiantCreateXlsx } from '../../models/etudiant.model';
import { EtudiantServiceImpl } from '../../services/impl/etudiant.service.impl';
import { HttpResponse } from '@angular/common/http';
import { response } from 'express';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';

@Component({
  selector: 'app-groups',
  standalone: true,
  imports: [NavComponent, FootComponent, RouterLink, RouterLinkActive, CommonModule, FormsModule],
  templateUrl: './groups.component.html',
  styleUrl: './groups.component.css'
})
export class GroupsComponent implements OnInit{
  liste: number = 0;
  listeResponse?: RestResponse<ListeModel>;
  etdResponse?: RestResponse<EtudiantCreateXlsx[]>;
  groupResponse?: RestResponse<GroupeModel[]>;
  user?:LogUser;
  libelle:string = ''
  error:boolean = false
  constructor(private router:Router, private authService:AuthServiceImpl, private groupeService:GroupeServiceImpl, private listeService:ListeServiceImpl, private apiService:ApiService, private etudiantService:EtudiantServiceImpl) { }

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
    }
    this.refresh(this.liste);
    if (this.groupResponse) {
      console.log(this.groupResponse);
    } 
  }

  printXls(){
    this.apiService.getExcelSheet(this.liste).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = `${this.listeResponse?.results.libelle}.xlsx`;
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
      this.router.navigate(['/app/liste-membre']);
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

  refresh(liste:number=0, page:number=0){
    this.groupeService.findAll(liste, page).subscribe(data=>this.groupResponse=data);
  }
  paginate(page:number){
    this.refresh(this.liste, page)
  }

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }

  reloadPage() {
    window.location.reload();
  }

  clearData(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('ecoleListe');
      localStorage.removeItem('tailleGrp')
      localStorage.removeItem('nomGrp');
      localStorage.removeItem('anneeListe');
    }
    
  }

}
