import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { AnneeModel } from '../../models/annee.model';
import { LogUser } from '../../models/user.model';
import { AnneeServiceImpl } from '../../services/impl/annee.service.impl';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { Router } from '@angular/router';
import { PaginatorService } from '../../services/pagination.service';

@Component({
  selector: 'app-annee',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  templateUrl: './annee.html',
  styleUrl: './annee.css'
})
export class Annee implements OnInit{
  anneeResponse?: RestResponse<AnneeModel[]>;
  keyword:string = '';
  libelle:string = '';
  msg:string = '';
  entity:number = 0;
  error:boolean = false;
  ajout:boolean = false;
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private anneeService:AnneeServiceImpl, private authService:AuthServiceImpl){}
  
  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }
    this.filter()
  }

  addObj(){
    if(this.libelle != ''){
      this.anneeService.addAnnee({ data: this.libelle }).subscribe((response:RequestResponse) => {
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
      this.libelle ='';
    }else{
      console.log('input is EMPTY!!!');
    }
  }

  modifEnt(id:any, kw:string = ''){
    console.log(id, kw)
    this.anneeService.modifAnnee(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette entité existe deja dans cette organisation')
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
  }

  changeEnt(val:any, l:any=''){
    this.entity = val;
    this.libelle = l
  }

  setMsg(msg:string = ''){
    this.msg = msg;
  }

  refresh(page:number=0,keyword:string=""){
    this.anneeService.findAllPg(page,keyword).subscribe(data=>this.anneeResponse=data);
  }
  paginate(page:number){
    this.filter(page, this.keyword)
  }
  filter(page:number=0, keyword:string=""){
    this.refresh(page,keyword)
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
