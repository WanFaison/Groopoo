import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { EcoleModel } from '../../models/ecole.model';
import { EtageModel } from '../../models/etage.model';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { Router } from '@angular/router';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { EtageServiceImpl } from '../../services/impl/etage.service.impl';
import { PaginatorService } from '../../services/pagination.service';

@Component({
  selector: 'app-etage',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  templateUrl: './etage.html',
  styleUrl: './etage.css'
})
export class Etage implements OnInit{
  ecoleResponse?: RestResponse<EcoleModel[]>;
  etageResponse?: RestResponse<EtageModel[]>;
  keyword:string = '';
  libelle:string = '';
  msg:string = '';
  ajout:boolean = false;
  error:boolean = false;
  ecole:number = 0;
  entity:number = 0;
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private authService:AuthServiceImpl, private etageService:EtageServiceImpl, private ecoleService:EcoleServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }
    this.filter()
  }

  checkCorrect(){
    if(this.ecole<1){
      this.error = true;
      this.setMsg("Choissisez une organisation");
    }else{
      this.addObj()
    }
  }

  modifEnt(id:any, kw:string = ''){
    console.log(id, kw)
    this.etageService.modifEtage(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette étage existe deja dans cette organisation')
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
      this.etageService.addEtage({libelle: this.libelle, ecole: this.ecole})
        .subscribe((response:RequestResponse) =>{
          console.log('Response from back-end:', response);
          if(response.data != 0){
            this.error = true;
            this.setMsg('Cette étage existe deja dans cette organisation')
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

  changeEnt(val:any, l:any=''){
    this.entity = val;
    this.libelle = l
  }

  setMsg(msg:string = ''){
    this.msg = msg;
  }

  refresh(page:number=0,keyword:string="",ecole:number=0){
    this.etageService.findAllPg(page,keyword, ecole).subscribe(data=>this.etageResponse=data);
  }
  paginate(page:number){
    this.filter(page, this.keyword, this.ecole)
  }
  filter(page:number=0, keyword:string="", ecole:number=0){
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    this.refresh(page,keyword,ecole) 
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
