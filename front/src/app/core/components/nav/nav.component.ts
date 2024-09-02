import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';

@Component({
  selector: 'app-nav',
  standalone: true,
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './nav.component.html',
  styleUrl: './nav.component.css'
})
export class NavComponent implements OnInit{
  constructor(private authService:AuthServiceImpl, private router:Router){
  }

  ngOnInit(): void {
  }

  onLogout():void{
    this.authService.logout();
    this.router.navigateByUrl("/authentification/login");
  }

}
