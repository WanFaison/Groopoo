import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { LogUser } from '../../models/user.model';
import { CommonModule } from '@angular/common';
import { RestResponse } from '../../models/rest.response';
import { EcoleModel } from '../../models/ecole.model';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';

@Component({
  selector: 'app-nav',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, CommonModule],
  templateUrl: './nav.component.html',
  styleUrl: './nav.component.css'
})
export class NavComponent implements OnInit{
  user?:LogUser;
  ecole:number =0
  ecoleResponse?:RestResponse<EcoleModel>
  constructor(private authService:AuthServiceImpl, private router:Router, private ecoleService:EcoleServiceImpl)
  {}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if (typeof window !== 'undefined' && localStorage && this.user?.role == 'ROLE_ECOLE_ADMIN'){
      this.ecole = parseInt(localStorage.getItem('ecoleListe') || '0', 10);
      this.ecoleService.findById(this.ecole).subscribe(data=>this.ecoleResponse=data)
    }
  }

  onLogout():void{
    this.authService.logout();
    this.router.navigateByUrl("/authentification/login");
  }

}
