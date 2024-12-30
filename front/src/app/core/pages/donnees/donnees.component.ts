import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
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
import { SalleModel } from '../../models/salle.model';
import { SalleServiceImpl } from '../../services/impl/salle.service.impl';
import { EtageModel } from '../../models/etage.model';
import { EtageServiceImpl } from '../../services/impl/etage.service.impl';
import { response } from 'express';
import { CoachModel } from '../../models/coach.model';
import { CoachServiceImpl } from '../../services/impl/coach.service.impl';
import { PaginatorService } from '../../services/pagination.service';

@Component({
  selector: 'app-donnees',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, ReactiveFormsModule, CommonModule],
  templateUrl: './donnees.component.html',
  styleUrl: './donnees.component.css'
})
export class DonneesComponent implements OnInit{
  state:any;
  coachForm:FormGroup;
  ecoleResponse?: RestResponse<EcoleModel[]>;
  anneeResponse?: RestResponse<AnneeModel[]>;
  salleResponse?: RestResponse<SalleModel[]>;
  etageResponse?: RestResponse<EtageModel[]>;
  coachResponse?: RestResponse<CoachModel[]>;
  coachRequest?: RestResponse<CoachModel>;
  keyword:string = '';
  ecole:number = 0;
  etage:number = 0;
  libelle:string = '';
  msg:string = '';
  ajout:boolean = false;
  error:boolean = false;
  entity:number = 0;
  entString:string = '';
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private formBuilder: FormBuilder, private http:HttpClient, private authService:AuthServiceImpl, private coachService:CoachServiceImpl, private etageService:EtageServiceImpl, private salleService:SalleServiceImpl, private ecoleService:EcoleServiceImpl, private anneeService:AnneeServiceImpl)
  {
    this.coachForm = this.formBuilder.group({
      nom: ['', Validators.required],
      prenom: ['', Validators.required],
      tel: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      ecole: ['', Validators.required],
      option1: false,
      option2: false,
      option3: false
    });
  }

  get nomControl() {
    return this.coachForm.get('nom');
  }
  get prenomControl() {
    return this.coachForm.get('prenom');
  }
  get emailControl() {
    return this.coachForm.get('email');
  }
  get ecoleId(): any[] {
    return this.coachForm.get('ecole')?.value || [];
  }
  
  ngOnInit(): void {
    this.user = this.authService.getUser()
    if(this.user?.role != 'ROLE_ADMIN'){
      this.router.navigate(['/app/not-found'])
    }
    this.coachForm.valueChanges.subscribe(value => {
      console.log(this.coachForm.value)
      console.log(this.coachRequest?.results)
    });

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
                this.setMsg('Cette entité existe deja dans cette organisation')
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }else if(state == 1){
      this.ecoleService.modifEcole(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette entité existe deja dans cette organisation')
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }else if(state == 2){
      this.salleService.modifSalle(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette salle existe deja dans cette organisation')
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }else if(state == 3){
      this.etageService.modifEtage(id, kw).subscribe(
        response=>{
              console.log(response.message)
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette étage existe deja dans cette organisation')
              }else{
                this.reloadPage(); 
              } 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }else if(state == 4){
      this.coachService.modifCoach(id).subscribe(
        response=>{
              //console.log(response.message)
              this.reloadPage(); 
            },        
        error => {
              console.error('Error sending data', error);
            })
    }
  }

  addObj(){
    if(this.state == 4){
      this.coachService.addCoach(this.coachForm.value, this.coachRequest?.results.id ?? 0)
      .subscribe((response:RequestResponse) =>{
        console.log('Response from back-end:', response);
        if(response.data != 0){
          this.error = true;
          this.setMsg('Cette entité existe deja dans cette organisation')
        }else{
          this.ajout = true;
        }
      }, error => {
        console.error('Error:', error);
      });
    }

    if(this.libelle != ''){
      if(this.state == 0){
        this.anneeService.addAnnee({ data: this.libelle }).subscribe((response:RequestResponse) => {
              console.log('Response from back-end:', response);
              if(response.data != 0){
                this.error = true;
                this.setMsg('Cette entité existe deja dans cette organisation')
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
                this.setMsg('Cette entité existe deja dans cette organisation')
              }else{
                this.ajout = true;
              }
            }, error => {
              console.error('Error:', error);
            });
      }else if(this.state == 2){
        this.salleService.addSalle({libelle: this.libelle, etage: this.etage})
        .subscribe((response:RequestResponse) =>{
          console.log('Response from back-end:', response);
          if(response.data != 0){
            this.error = true;
            this.setMsg('Cette salle existe deja dans cette organisation')
          }else{
            this.ajout = true;
          }
        }, error => {
          console.error('Error:', error);
        });
      }else if(this.state == 3){
        this.etageService.addEtage({libelle: this.libelle, ecole: this.ecole})
        .subscribe((response:RequestResponse) =>{
          console.log('Response from back-end:', response);
          if(response.data != 0){
            this.error = true;
            this.setMsg('Cette étage existe deja dans cette organisation')
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

  checkCorrect(){
    if(this.state == 2 && this.etage<1){
      this.error = true;
      this.setMsg("Choissisez un étage");
    }else if(this.state == 3 && this.ecole<1){
      this.error = true;
      this.setMsg("Choissisez une organisation");
    }else if(this.state == 4 && this.isFormEmpty(this.coachForm)){
      this.error = true;
      this.setMsg("Entrez tout les informations du coach");
    }else{
      this.addObj()
    }
  }

  setCoachModif(coachId:number){
    this.coachService.findById(coachId).subscribe(data=>{
      this.coachRequest=data;
      this.coachForm = this.formBuilder.group({
        nom: [this.coachRequest?.results.nom, Validators.required],
        prenom: [this.coachRequest?.results.prenom, Validators.required],
        tel: [this.coachRequest?.results.tel, Validators.required],
        email: [this.coachRequest?.results.email, [Validators.required, Validators.email]],
        ecole: [this.coachRequest?.results.ecoleId, Validators.required],
        option1: this.coachRequest?.results.etat == 'Debutant' ?  true : false,
        option2: this.coachRequest?.results.etat == 'Moyen' ?  true : false,
        option3: this.coachRequest?.results.etat == 'Senior' ?  true : false
      });
    });
  }

  changeState(val:any){
    this.state = val;
    localStorage.setItem('stateMenu', this.state);
    switch (this.state) {
      case 0:
        this.entString = 'année'
        break;
      case 1:
        this.entString = 'organisation'
        break;
      case 2:
        this.entString = 'salle'
        break;
      case 3:
        this.entString = 'étage'
        break;
      case 3:
        this.entString = 'coach'
        break;
    
      default:
        this.entString = 'année'
        break;
    }

    this.filter()
  }
  changeEnt(val:any, l:any=''){
    this.entity = val;
    this.libelle = l
  }

  loadEtagesByEcole(){
    this.etageService.findAllByEcole(this.ecole).subscribe(data=>this.etageResponse=data)
  }

  setMsg(msg:string = ''){
    this.msg = msg;
  }

  isFormEmpty(formGroup:FormGroup): boolean {
    for (const key in formGroup.controls) {
      if (formGroup.controls[key].invalid) {
        return true;
      }
    }
    return false;
  }

  refresh(page:number=0,keyword:string=""){
    this.anneeService.findAllPg(page,keyword).subscribe(data=>this.anneeResponse=data);
    this.ecoleService.findAllPg(page,keyword).subscribe(data=>this.ecoleResponse=data);
  }
  paginate(page:number){
    this.filter(page, this.keyword, this.ecole)
  }
  filter(page:number=0, keyword:string="", ecole:number=0){
    if(this.state == 2){
      this.salleService.findAllPg(page, keyword, ecole).subscribe(data=>this.salleResponse=data)
      this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    }else if(this.state == 3){
      this.etageService.findAllPg(page, keyword, ecole).subscribe(data=>this.etageResponse=data);
      this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    }else if(this.state == 4){
      this.coachService.findAllPg(page, keyword, ecole).subscribe(data=>this.coachResponse=data);
      this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    }else{
      this.refresh(page,keyword)
    }
    
  }

  getPageRange(currentPage:any, totalPages:any): number[] {
    return this.paginatorService.getPageRange(currentPage, totalPages)
  }

  reloadPage() {
    this.libelle ='';
    this.error = false;
    return this.paginatorService.reloadPage();
  }

}
