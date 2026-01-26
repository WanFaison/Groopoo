import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { PaginatorService } from '../../services/pagination.service';
import { Router } from '@angular/router';
import { LogUser } from '../../models/user.model';
import { ThemeServiceImpl } from '../../services/impl/theme.service.impl';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { ThemeModel } from '../../models/theme.model';

@Component({
  selector: 'app-theme',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  templateUrl: './theme.html',
  styleUrl: './theme.css'
})
export class Theme implements OnInit{
  themeResponse?:RestResponse<ThemeModel[]>;
  keyword:string = '';
  libelle:string = '';
  msg:string = '';
  entity:number = 0;
  ajout:boolean = false;
  error:boolean = false;
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private authService:AuthServiceImpl, private themeService:ThemeServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }

    this.filter()
  }

  addObj(){
    if(this.libelle != ''){
      this.themeService.addTheme({ data: this.libelle }).subscribe((response:RequestResponse) => {
              console.log('Response from back-end:', response);
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette thematique existe deja')
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
    this.themeService.modifTheme(id, kw).subscribe(
      response=>{
            console.log(response.message)
            if(response.data != 0){
              this.error = true;
              this.setMsg('Cette thematique existe deja')
            }else{
              this.reloadPage(); 
            } 
          },        
      error => {
            console.error('Error sending data', error);
          })
  }

  setMsg(msg:string = ''){
    this.msg = msg;
  }

  changeEnt(val:any, l:any=''){
    this.entity = val;
    this.libelle = l
  }

  refresh(page:number=0,keyword:string=""){
    this.themeService.findAllPg(page, keyword).subscribe(data=>this.themeResponse=data);
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
