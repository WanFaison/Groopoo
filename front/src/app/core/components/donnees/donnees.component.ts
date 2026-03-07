import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { FootComponent } from '../foot/foot.component';
import { NavComponent } from '../nav/nav.component';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { Annee } from "../../pages/annee/annee";
import { Coach } from "../../pages/coach/coach";
import { Organisation } from "../../pages/organisation/organisation";
import { Salle } from "../../pages/salle/salle";
import { Etage } from "../../pages/etage/etage";
import { Theme } from "../../pages/theme/theme";
import { Router } from '@angular/router';

@Component({
    selector: 'app-donnees',
    imports: [NavComponent, FootComponent, FormsModule, ReactiveFormsModule, CommonModule, Annee, Coach, Organisation, Salle, Etage, Theme],
    templateUrl: './donnees.component.html',
    styleUrl: './donnees.component.css'
})
export class DonneesComponent implements OnInit{
  state:any= 0;
  user?:LogUser
  constructor(private router:Router, private authService:AuthServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role == 'ROLE_VISITEUR' || 'ROLE_COACH'){
      this.router.navigate(['/app/not-found'])
    }
    if(typeof window !== 'undefined' && localStorage){
      this.state = parseInt(localStorage.getItem('stateMenu') || '0', 10);
    }
  }

  changeState(val:any){
    this.state = val;
    localStorage.setItem('stateMenu', this.state);
  }

}
