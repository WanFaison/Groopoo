import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { PaginatorService } from '../../services/pagination.service';
import { RestResponse } from '../../models/rest.response';
import { ListeModel } from '../../models/liste.model';
import { SalleModel } from '../../models/salle.model';
import { SalleServiceImpl } from '../../services/impl/salle.service.impl';

@Component({
    selector: 'app-form-salle',
    imports: [CommonModule, FormsModule, ReactiveFormsModule],
    templateUrl: './form-salle.component.html',
    styleUrl: './form-salle.component.css'
})
export class FormSalleComponent implements OnInit{
  salleForm: {id: number; add: boolean; libelle: string}[] = [];
  salleResponse?:RestResponse<SalleModel[]>;
  salleActiveResponse?:RestResponse<SalleModel[]>;
  listeResponse?: RestResponse<ListeModel>;
  liste: number = 0;
  ecole: number = 0;
  cnt: number = 0;
  cnt2: number = 0;
  msg: string = '';
  keyword: string = '';
  coachForm: {id: number; add: boolean; nom: string}[] = [];
  constructor(private paginatorService:PaginatorService, private listeService:ListeServiceImpl, private salleService:SalleServiceImpl){}

  ngOnInit(): void {
    if(typeof window != 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>{
        this.listeResponse=data;
        if(this.listeResponse){this.ecole = this.listeResponse?.results.ecoleId}

        if(this.salleForm.length<1){
          this.salleService.findByList(this.liste).subscribe(data=>this.salleActiveResponse=data);
          this.loadActiveSalles()
          localStorage.setItem('salleForm', JSON.stringify(this.salleForm));
        }
      });                                                    
    }
    const cform = localStorage.getItem('coachForm');
    this.coachForm = cform? JSON.parse(cform) : [];

    const form = localStorage.getItem('salleForm')
    this.salleForm = form? JSON.parse(form) : []; 
    this.refresh(this.ecole)
  }

  loadActiveSalles() {
    this.salleActiveResponse?.results.forEach(item => {
      if(!this.checkAdded(item.id)){
        this.salleForm.push({
        id: item.id,
        add: true,
        libelle: item.libelle
      })}
    });
  }

  checkAdded(salleId: number) {
    const existingEntry = this.salleForm.find(entry => entry.id === salleId);
    if (existingEntry) {
      return existingEntry.add;
    }
    return false;
  }

  onFormChange(salleId: number, salleLibelle: string, event: Event) {
    const checked = (event.target as HTMLInputElement).checked;
    const existingEntry = this.salleForm.find(entry => entry.id === salleId);
    
    if (existingEntry) {
      existingEntry.add = checked;
    } else {
      this.salleForm.push({
        id: salleId,
        add: checked,
        libelle: salleLibelle
      });
    }
    localStorage.setItem('salleForm', JSON.stringify(this.salleForm))
    console.log(this.salleForm);
  }

  checkValidForm(){
    this.salleForm.forEach(item=>{ if(item.add){this.cnt +=1} })
    this.coachForm.forEach(item=>{ if(item.add){this.cnt2 +=1} })
    return (this.cnt2 <= this.cnt);
  }

  changeState(num: any) {
    localStorage.setItem('stateListeMenu', num);
    this.reloadPage()
  }

  refresh(ecole: number = this.ecole, page:number=0, keyword:string='') {
    this.salleService.findAllPg(page, keyword, ecole)
                      .subscribe(data=>{this.salleResponse=data});
  }

  filter() {                                                                                                                                                   
    this.refresh(this.ecole, 0, this.keyword);     
  }
  
  paginate(page:number){
    this.refresh(this.ecole, page, this.keyword)
  }

  reloadPage(){
    this.paginatorService.reloadPage();
  }

}
