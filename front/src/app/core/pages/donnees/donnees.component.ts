import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { RestResponse } from '../../models/rest.response';
import { EcoleModel } from '../../models/ecole.model';
import { AnneeModel } from '../../models/annee.model';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';
import { AnneeServiceImpl } from '../../services/impl/annee.service.impl';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-donnees',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './donnees.component.html',
  styleUrl: './donnees.component.css'
})
export class DonneesComponent implements OnInit{
  state:number = 0;
  ecoleResponse?: RestResponse<EcoleModel[]>;
  anneeResponse?: RestResponse<AnneeModel[]>;
  keyword:string = '';
  libelle:string = '';
  entity:number = 0;
  constructor(private router:Router, private http:HttpClient, private ecoleService:EcoleServiceImpl, private anneeService:AnneeServiceImpl){}
  
  ngOnInit(): void {
    this.filter()
  }

  modifEnt(state:any, id:any, kw:string = ''){
    if(state == 0){
      this.anneeService.modifAnnee(id, kw).subscribe(
        response=>{
              console.log(response.message)
              this.reloadPage(); 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }else{
      this.ecoleService.modifEcole(id, kw).subscribe(
        response=>{
              console.log(response.message)
              this.reloadPage(); 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }
    this.reloadPage();
  }

  addObj(){
    if(this.libelle != ''){
      if(this.state == 0){
          this.http.post(this.anneeService.getAddUrl(), { data: this.libelle })
            .subscribe(response => {
              console.log('Response from back-end:', response);
            }, error => {
              console.error('Error:', error);
            });
      }else if(this.state == 1){
        this.http.post(this.ecoleService.getAddUrl(), { data: this.libelle })
            .subscribe(response => {
              console.log('Response from back-end:', response);
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
    //this.reloadPage()
  }
  changeEnt(val:any, l:any){
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
  }

}
