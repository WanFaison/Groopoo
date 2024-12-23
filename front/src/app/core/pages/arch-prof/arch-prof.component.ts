import { Component, OnInit } from '@angular/core';
import { RestResponse } from '../../models/rest.response';
import { UserModel } from '../../models/user.model';
import { EcoleModel } from '../../models/ecole.model';
import { PaginatorService } from '../../services/pagination.service';
import { UserService } from '../../services/user.service';
import { UserServiceImpl } from '../../services/impl/user.service.impl';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-arch-prof',
  standalone: true,
  imports: [FormsModule, CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './arch-prof.component.html',
  styleUrl: './arch-prof.component.css'
})
export class ArchProfComponent implements OnInit{
  userResponse?: RestResponse<UserModel[]>;
  ecoleResponse?:RestResponse<EcoleModel[]>;
  keyword:string = '';
  ecole:number = 0;
  userId:number = 0;
  constructor(private ecoleService:EcoleServiceImpl, private paginatorService:PaginatorService, private userService:UserServiceImpl){}
  
  ngOnInit(): void {
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse = data)
    this.filter();
  }

  dearchiveUser(id: number) {
    this.userService.modifUser(id).subscribe(
      response=>{
            console.log(response.message)
            this.reloadPage(); 
          },        
      error => {
            console.error('Error sending data', error);
          })
  }

  refresh(page:number=0,keyword:string="", ecole:number =0){
    this.userService.findAllArchived(page,keyword, ecole).subscribe(data=>this.userResponse=data);
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
