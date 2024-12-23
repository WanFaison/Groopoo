import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { FootComponent } from '../foot/foot.component';
import { NavComponent } from '../nav/nav.component';
import { HomeComponent } from "../../pages/home/home.component";
import { ArchivedComponent } from "../../pages/archived/archived.component";
import { MembresComponent } from "../../pages/membres/membres.component";

@Component({
  selector: 'app-home-menu',
  standalone: true,
  imports: [CommonModule, NavComponent, FootComponent, HomeComponent, ArchivedComponent, MembresComponent],
  templateUrl: './home-menu.component.html',
  styleUrl: './home-menu.component.css'
})
export class HomeMenuComponent implements OnInit{
  state:any = 0;
  user?:LogUser;
  constructor(private authService:AuthServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(typeof window !== 'undefined' && localStorage){
      this.state = parseInt(localStorage.getItem('homeMenu') || '0', 10);
    }
  }

  changeState(num: number) {
    this.state = num;
    localStorage.setItem('homeMenu', this.state);
  }

}
