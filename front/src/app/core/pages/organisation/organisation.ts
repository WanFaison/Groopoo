import { Component, OnInit } from '@angular/core';
import { EcoleModel } from '../../models/ecole.model';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { Router } from '@angular/router';
import { PaginatorService } from '../../services/pagination.service';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { LogUser } from '../../models/user.model';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-organisation',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  templateUrl: './organisation.html',
  styleUrl: './organisation.css'
})
export class Organisation implements OnInit{
  ecoleResponse?: RestResponse<EcoleModel[]>;
  keyword:string = '';
  entity:number = 0;
  libelle:string = '';
  msg:string = '';
  ajout:boolean = false;
  error:boolean = false;
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private authService:AuthServiceImpl, private ecoleService:EcoleServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }

    this.filter()
  }

  addObj(){
    if(this.libelle != ''){
      this.ecoleService.addEcole({ data: this.libelle }).subscribe((response:RequestResponse) => {
              console.log('Response from back-end:', response);
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette organisation existe deja')
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
    this.ecoleService.modifEcole(id, kw).subscribe(
      response=>{
            console.log(response.message)
            if(response.data != 0){
              this.error = true;
              this.setMsg('Cette organisation existe deja')
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
    this.ecoleService.findAllPg(page,keyword).subscribe(data=>this.ecoleResponse=data);
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
