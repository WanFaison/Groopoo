import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { AnneeServiceImpl } from '../../services/impl/annee.service.impl';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { EtudiantServiceImpl } from '../../services/impl/etudiant.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { AnneeModel } from '../../models/annee.model';
import { EcoleModel } from '../../models/ecole.model';
import { EtudiantCreateXlsx } from '../../models/etudiant.model';
import { ListeModel } from '../../models/liste.model';
import { RestResponse } from '../../models/rest.response';
import { LogUser } from '../../models/user.model';

@Component({
  selector: 'app-archived',
  standalone: true,
  imports: [NavComponent, FootComponent, RouterLink, RouterLinkActive, CommonModule, FormsModule],
  templateUrl: './archived.component.html',
  styleUrl: './archived.component.css'
})
export class ArchivedComponent implements OnInit{
  response?: RestResponse<ListeModel[]>;
  listeResponse?: RestResponse<ListeModel>;
  anneeResponse?: RestResponse<AnneeModel[]>;
  etdResponse?: RestResponse<EtudiantCreateXlsx[]>;
  ecoleResponse?: RestResponse<EcoleModel[]>;
  keyword: string = '';
  annee: number = 0;
  ecole: number = 0;
  selectedEcole: number = 0; 
  liste: number = 0;
  user?:LogUser
  constructor(private router:Router, private authService:AuthServiceImpl, private ecoleService:EcoleServiceImpl, private listeService:ListeServiceImpl, private anneeService:AnneeServiceImpl, private etudiantService:EtudiantServiceImpl){}

  ngOnInit(): void {
    this.anneeService.findAll().subscribe(data=>this.anneeResponse=data);
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    this.user = this.authService.getUser();
    if(this.user?.role == 'ROLE_ECOLE_ADMIN'){
      if(!localStorage.getItem('ecoleListe')){
        localStorage.setItem('ecoleListe', this.user.ecole[0])
      }
    }
    this.filter()
  }

  changeEcole(index:any){
    localStorage.setItem('ecoleListe', this.user?.ecole[this.selectedEcole])
    this.reloadPage()
  }

  viewListe(liste:any){
    if(typeof window !== 'undefined' && localStorage){
      localStorage.setItem('newListe', liste);
    }
    this.router.navigate(['/app/view-jours']);
  }

  deArchiverListe(liste:any, motif:string='archive'){
    this.listeService.modifListe(liste, motif).subscribe(
      response=>{
            console.log(response.message)
            this.reloadPage(); 
          },        
      error => {
            console.error('Error sending data', error);
          })
  }

  refresh(page:number=0,keyword:string=this.keyword, annee:number=0, ecole:number = this.ecole){
    if(this.user){
      if (typeof window !== 'undefined' && localStorage && this.user?.role == 'ROLE_ECOLE_ADMIN'){
        this.ecole = parseInt(localStorage.getItem('ecoleListe') || '0', 10);
        this.listeService.findAll(page,keyword, annee, this.ecole, 1).subscribe(data=>this.response=data);
      }else{
        this.listeService.findAll(page,keyword, annee, ecole, 1).subscribe(data=>this.response=data);
      }
      
    }
    this.ecole = 0
    this.annee =0;
    this.keyword = '';
  }
  paginate(page:number){
    this.refresh(page)
  }
  filter(page:number=0, keyword:string=this.keyword, annee:number=0, ecole:number=this.ecole){
    this.refresh(page, keyword, annee, ecole)
  }

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }
  getPageRange(currentPage:any, totalPages:any): number[] {
    const start = Math.max(currentPage - 3, 1);
    const end = Math.min(currentPage + 3, totalPages);
      
    return this.pages(start, end);
  }

  reloadPage() {
    window.location.reload();
  }

  clearData(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('tailleGrp')
      localStorage.removeItem('nomGrp');
      localStorage.removeItem('anneeListe');
    }
    
  }
}
