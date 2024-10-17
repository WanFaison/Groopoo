import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { EcoleModel } from '../../models/ecole.model';
import { AnneeModel } from '../../models/annee.model';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { AnneeServiceImpl } from '../../services/impl/annee.service.impl';
import { HttpClient } from '@angular/common/http';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';

@Component({
  selector: 'app-donnees',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './donnees.component.html',
  styleUrl: './donnees.component.css'
})
export class DonneesComponent implements OnInit{
  state:any;
  ecoleResponse?: RestResponse<EcoleModel[]>;
  anneeResponse?: RestResponse<AnneeModel[]>;
  keyword:string = '';
  libelle:string = '';
  ajout:boolean = false;
  error:boolean = false;
  entity:number = 0;
  user?:LogUser
  constructor(private router:Router, private http:HttpClient, private authService:AuthServiceImpl, private ecoleService:EcoleServiceImpl, private anneeService:AnneeServiceImpl){}
  
  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }

    if(typeof window !== 'undefined' && localStorage){
      this.state = parseInt(localStorage.getItem('stateMenu') || '0', 10);
    }
    this.filter()
  }

  modifEnt(state:any, id:any, kw:string = ''){
    console.log(state, id, kw)
    if(state == 0){
      this.anneeService.modifAnnee(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }else{
      this.ecoleService.modifEcole(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }
  }

  addObj(){
    if(this.libelle != ''){
      if(this.state == 0){
        this.anneeService.addAnnee({ data: this.libelle }).subscribe((response:RequestResponse) => {
              console.log('Response from back-end:', response);
              if(response.data != 0){
                this.error = true;
              }else{
                this.ajout = true;
              }
            }, error => {
              console.error('Error:', error);
            });
      }else if(this.state == 1){
        this.ecoleService.addEcole({ data: this.libelle }).subscribe((response:RequestResponse) => {
              console.log('Response from back-end:', response);
              if(response.data != 0){
                this.error = true;
              }else{
                this.ajout = true;
              }
            }, error => {
              console.error('Error:', error);
            });
      }
      this.libelle ='';
    }else{
      console.log('input is EMPTY!!!');
    }
  }

  changeState(val:any){
    this.state = val;
    localStorage.setItem('stateMenu', this.state);
    //this.reloadPage()
  }
  changeEnt(val:any, l:any=''){
    this.entity = val;
    this.libelle = l
  }

  refresh(page:number=0,keyword:string=""){
    this.anneeService.findAllPg(page,keyword).subscribe(data=>this.anneeResponse=data);
    this.ecoleService.findAllPg(page,keyword).subscribe(data=>this.ecoleResponse=data);
  }
  paginate(page:number){
    this.refresh(page)
  }
  filter(page:number=0, keyword:string=this.keyword){
    this.refresh(page,keyword)
  }

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }

  reloadPage() {
    window.location.reload();
    this.libelle ='';
    this.error = false;
  }

}
