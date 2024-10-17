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

@Component({
  selector: 'app-profiles',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './profiles.component.html',
  styleUrl: './profiles.component.css'
})
export class ProfilesComponent implements OnInit{
  userResponse?: RestResponse<UserModel[]>;
  ecoleResponse?:RestResponse<EcoleModel[]>;
  keyword:string = '';
  ecole:number = 0;
  userId:number =0;
  user?:LogUser
  constructor(private router:Router, private http:HttpClient, private authService:AuthServiceImpl, private userService:UserServiceImpl, private ecoleService:EcoleServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }

    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse = data)
    this.filter();
  }

  saveUtilisateur(value: any) {
    localStorage.setItem('utilisateurId', value);
    //this.router.navigate(['/app/view-groups']);
  }

  archiveUser(user:number=0){
    this.userService.modifUser(user).subscribe(
      response=>{
            console.log(response.message)
            this.reloadPage(); 
          },        
      error => {
            console.error('Error sending data', error);
          })
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

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }

  reloadPage() {
    window.location.reload();
  }

}
