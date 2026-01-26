import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { SalleModel } from '../../models/salle.model';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { SalleServiceImpl } from '../../services/impl/salle.service.impl';
import { PaginatorService } from '../../services/pagination.service';
import { EcoleModel } from '../../models/ecole.model';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { EtageModel } from '../../models/etage.model';
import { EtageServiceImpl } from '../../services/impl/etage.service.impl';

@Component({
  selector: 'app-salle',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  templateUrl: './salle.html',
  styleUrl: './salle.css'
})
export class Salle implements OnInit{
  ecoleResponse?: RestResponse<EcoleModel[]>;
  salleResponse?: RestResponse<SalleModel[]>;
  etageResponse?: RestResponse<EtageModel[]>;
  ecole:number = 0;
  entity:number = 0;
  etage:number = 0;
  keyword:string = '';
  libelle:string = '';
  msg:string = '';
  error:boolean = false;
  ajout:boolean = false;
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private authService:AuthServiceImpl, private salleService:SalleServiceImpl, private ecoleService:EcoleServiceImpl, private etageService:EtageServiceImpl){}
  
  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }

    this.filter()
  }

  modifEnt(id:any, kw:string = ''){
    console.log(id, kw)
    this.salleService.modifSalle(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette salle existe deja dans cette organisation')
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
  }

  addObj(){
    if(this.libelle != ''){
      this.salleService.addSalle({libelle: this.libelle, etage: this.etage})
        .subscribe((response:RequestResponse) =>{
          console.log('Response from back-end:', response);
          if(response.data != 0){
            this.error = true;
            this.setMsg('Cette salle existe deja dans cette organisation')
          }else{
            this.ajout = true;
          }
        }, error => {
          console.error('Error:', error);
        });
      this.libelle ='';
    }else{
      console.log('input is EMPTY!!!');
    }
  }

  checkCorrect(){
    if(this.etage<1){
      this.error = true;
      this.setMsg("Choissisez un étage");
    }else{
      this.addObj()
    }
  }

  changeEnt(val:any, l:any=''){
    this.entity = val;
    this.libelle = l
  }

  loadEtagesByEcole(){
    this.etageService.findAllByEcole(this.ecole).subscribe(data=>this.etageResponse=data)
  }

  setMsg(msg:string = ''){
    this.msg = msg;
  }

  refresh(page:number=0,keyword:string="",ecole:number=0){
    this.salleService.findAllPg(page,keyword, ecole).subscribe(data=>this.salleResponse=data);
  }
  paginate(page:number){
    this.filter(page, this.keyword, this.ecole)
  }
  filter(page:number=0, keyword:string="", ecole:number=0){
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    this.refresh(page,keyword, ecole)
  }

  getPageRange(currentPage:any, totalPages:any): number[] {
    return this.paginatorService.getPageRange(currentPage, totalPages)
  }

  reloadPage() {
    this.libelle ='';
    this.error = false;
    return this.paginatorService.reloadPage();
  }
}
