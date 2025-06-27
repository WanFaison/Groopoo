import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { UserServiceImpl } from '../../services/impl/user.service.impl';
import { RestResponse } from '../../models/rest.response';
import { LogUser, UserModel } from '../../models/user.model';
import { EcoleModel } from '../../models/ecole.model';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { PaginatorService } from '../../services/pagination.service';

@Component({
    selector: 'app-profiles',
    imports: [FormsModule, CommonModule, RouterLink, RouterLinkActive],
    templateUrl: './profiles.component.html',
    styleUrl: './profiles.component.css'
})
export class ProfilesComponent implements OnInit{
  userResponse?: RestResponse<UserModel[]>;
  ecoleResponse?:RestResponse<EcoleModel[]>;
  keyword:string = '';
  ecole:number = 0;
  userId:number = 0;
  constructor(private router:Router, private paginatorService:PaginatorService, private http:HttpClient, private authService:AuthServiceImpl, private userService:UserServiceImpl, private ecoleService:EcoleServiceImpl){}

  ngOnInit(): void {
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse = data)
    this.filter();
  }

  saveUtilisateur(value: any) {
    localStorage.setItem('utilisateurId', value);
  }

  archiveUser(user:number=0, motif:number =0){
    if(motif == 0){
      this.userService.modifUser(user).subscribe(
        response=>{
              console.log(response.message)
              this.reloadPage(); 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }else{
      this.userService.modifUser(user, motif).subscribe(
        response=>{
              console.log(response.message)
              this.reloadPage(); 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }
  }


  refresh(page:number=0,keyword:string="", ecole:number =0){
    this.userService.findAllPg(page,keyword, ecole).subscribe(data=>this.userResponse=data);
  }
  paginate(page:number){
    this.refresh(page)
  }
  filter(page:number=0, keyword:string=this.keyword, ecole:number=0){
    this.refresh(page,keyword, ecole)
  }

  getPageRange(currentPage:any, totalPages:any): number[] {
    return this.paginatorService.getPageRange(currentPage, totalPages)
  }

  reloadPage() {
    return this.paginatorService.reloadPage();
  }

}
