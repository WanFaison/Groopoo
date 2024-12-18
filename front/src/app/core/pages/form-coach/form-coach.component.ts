import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { HttpClient } from '@angular/common/http';
import { CoachServiceImpl } from '../../services/impl/coach.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { ListeModel } from '../../models/liste.model';
import { RestResponse } from '../../models/rest.response';
import { CoachModel } from '../../models/coach.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { LogUser } from '../../models/user.model';

@Component({
  selector: 'app-form-coach',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './form-coach.component.html',
  styleUrl: './form-coach.component.css'
})
export class FormCoachComponent implements OnInit{
  coachForm: {id: number; add: boolean; nom: string}[] = [];
  coachResponse?:RestResponse<CoachModel[]>;
  coachActiveResponse?:RestResponse<CoachModel[]>;
  listeResponse?: RestResponse<ListeModel>;
  user?:LogUser;
  liste: number = 0;
  msg:string = '';
  ecole: number = 0;
  keyword:string = '';

  constructor(private router:Router, private authService:AuthServiceImpl, private listeService:ListeServiceImpl, private formBuilder: FormBuilder, private coachService:CoachServiceImpl){}

  ngOnInit(): void {
    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);

      if(this.coachForm.length<1){
        this.coachService.findByListe(this.liste).subscribe(data=>this.coachActiveResponse=data);
        this.loadActiveCoaches()
        localStorage.setItem('coachForm', JSON.stringify(this.coachForm));
      }
      const form = localStorage.getItem('coachForm')
      this.coachForm = form? JSON.parse(form) : [];
    }
    this.refresh(this.liste)
  }

  loadActiveCoaches(){
    this.coachActiveResponse?.results.forEach(item => {
      this.coachForm.push({
        id: item.id,
        add: true,
        nom: item.nom
      })
    });
  }

  checkAdded(coachId: number){
    const existingEntry = this.coachForm.find(entry => entry.id === coachId);
    if (existingEntry) {
      return existingEntry.add;
    }
    return false;
  }

  onFormChange(coachId: number, coachNom: string, event:Event): void {
    const checked = (event.target as HTMLInputElement).checked;
    const existingEntry = this.coachForm.find(entry => entry.id === coachId);
    
    if (existingEntry) {
      existingEntry.add = checked;
    } else {
      this.coachForm.push({
        id: coachId,
        add: checked,
        nom: coachNom
      });
    }
    localStorage.setItem('coachForm', JSON.stringify(this.coachForm))
    console.log(this.coachForm);
  }

  checkValidForm(){
    return this.coachForm.some(data => data.add === true);
  }

  assignCoaches() {
    if (typeof window !== 'undefined' && localStorage){
      const coachs = localStorage.getItem('coachForm')
      if(coachs && JSON.parse(coachs).length>0 && this.checkValidForm()){
        const data = {
          liste: this.liste,
          coachs: coachs? JSON.parse(coachs):[]
        }
  
        this.coachService.assignCoaches(data).subscribe(
          response => {
            console.log(response)
            if(response.data != 0){
              this.msg = "Erreur! Vous essayez d'affecter plus de coachs que de salles disponibles"
            }else{
              localStorage.removeItem('coachForm');
              this.router.navigate(['/app/jury'])
            }
          },
          error => {
            localStorage.removeItem('coachForm');
            console.error('Error sending data', error);
          }
        )
      }else{
        this.msg = 'Aucun coach assigné'
      }
    }
    
  }

  refresh(liste:number=this.liste, page:number=0, keyword:string='', ecole:number=0){
    this.coachService.findAllPg(page, keyword, ecole, liste)
                      .subscribe(data=>{this.coachResponse=data});
  }
  filter(){
    this.refresh(this.liste, 0, this.keyword)
  }
  paginate(page:number){
    this.refresh(this.liste, page, this.keyword, this.ecole)
  }
}
