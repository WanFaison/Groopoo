import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive, RouterModule } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { HttpClient } from '@angular/common/http';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { RestResponse } from '../../models/rest.response';
import { JourModel } from '../../models/jour.model';
import { LogUser } from '../../models/user.model';
import { JourServiceImpl } from '../../services/impl/jour.service.impl';
import { ListeModel } from '../../models/liste.model';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { NgbDatepickerModule } from '@ng-bootstrap/ng-bootstrap';
import { response } from 'express';
import { PaginatorService } from '../../services/pagination.service';

@Component({
  selector: 'app-jours',
  standalone: true,
  imports: [FormsModule, CommonModule, RouterModule, NgbDatepickerModule],
  templateUrl: './jours.component.html',
  styleUrl: './jours.component.css'
})
export class JoursComponent implements OnInit{
  jourResponse?:RestResponse<JourModel[]>;
  listeResponse?:RestResponse<ListeModel>
  liste:any = 0
  jrs:any
  date:any =''
  libelle:string = ''
  error:boolean = false
  errMsg:string = ''
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private http:HttpClient, private authService:AuthServiceImpl, private jourService:JourServiceImpl, private listeService:ListeServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/view-groups'])
    }

    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '0', 10)
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data)
      this.jourService.findAllListe(this.liste).subscribe(data=>this.jourResponse = data);
    }
  }

  archJr() {
    this.jourService.modifJr(this.jrs).subscribe(
      response=>{
        console.log(response)
        this.reloadPage(); 
      },        
      error => {
            console.error('Error sending data', error);
      }
    )
  }

  consAbs(jr: any, num: any = 6) {
    localStorage.setItem('jrListe', jr)
    localStorage.setItem('stateListeMenu', num);
    this.reloadPage()
  }

  showNotes(){
    localStorage.setItem('stateListeMenu', '2');
    this.reloadPage()
  }

  addJr() {
    this.jourService.addJour(this.liste, this.date).subscribe(
      response=>{
        if(response.data != 0){
          //console.log(this.date)
          this.error = true;
          this.errMsg = response.message
        }else{
          this.reloadPage(); 
        }
      },        
      error => {
            console.error('Error sending data', error);
          }
    );
  }

  modifEnt(ent: any,lib: string) {
    this.listeService.modifListe(ent, 'modif', lib).subscribe(
      response=>{
        if(response.data != 0){
          this.error = true;
        }else{
          this.reloadPage(); 
        } 
      },        
      error => {
            console.error('Error sending data', error);
          });
  }

  reloadPage() {
    return this.paginatorService.reloadPage();
  }
}
