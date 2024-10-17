import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { LogUser } from '../../models/user.model';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-nav',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, CommonModule],
  templateUrl: './nav.component.html',
  styleUrl: './nav.component.css'
})
export class NavComponent implements OnInit{
  user?:LogUser;
  constructor(private authService:AuthServiceImpl, private router:Router){
  }

  ngOnInit(): void {
    this.user = this.authService.getUser();
  }

  onLogout():void{
    this.authService.logout();
    this.router.navigateByUrl("/authentification/login");
  }

}
