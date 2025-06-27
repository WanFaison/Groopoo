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
import { PaginatorService } from '../../services/pagination.service';

@Component({
    selector: 'app-archived',
    imports: [CommonModule, FormsModule],
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
  constructor(private router:Router, private paginatorService:PaginatorService, private authService:AuthServiceImpl, private ecoleService:EcoleServiceImpl, private listeService:ListeServiceImpl, private anneeService:AnneeServiceImpl, private etudiantService:EtudiantServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/not-found'])
    }
    this.anneeService.findAll().subscribe(data=>this.anneeResponse=data);
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    
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
    this.router.navigate(['/app/liste-menu']);
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
