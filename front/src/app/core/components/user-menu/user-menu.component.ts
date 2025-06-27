import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { LogUser } from '../../models/user.model';
import { FootComponent } from '../foot/foot.component';
import { NavComponent } from '../nav/nav.component';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { ProfilesComponent } from "../../pages/profiles/profiles.component";
import { Router } from '@angular/router';
import { ArchProfComponent } from "../../pages/arch-prof/arch-prof.component";

@Component({
    selector: 'app-user-menu',
    imports: [CommonModule, NavComponent, FootComponent, ProfilesComponent, ArchProfComponent],
    templateUrl: './user-menu.component.html',
    styleUrl: './user-menu.component.css'
})
export class UserMenuComponent implements OnInit{
  state:any = 0;
  user?:LogUser;
  constructor(private router:Router, private authService:AuthServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }

    if(typeof window !== 'undefined' && localStorage){
      this.state = parseInt(localStorage.getItem('userMenu') || '0', 10);
    }
  }

  changeState(num: number) {
    this.state = num;
    localStorage.setItem('userMenu', this.state);
  }

}
