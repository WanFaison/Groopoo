import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { CoachModel } from '../../models/coach.model';
import { EcoleModel } from '../../models/ecole.model';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { LogUser } from '../../models/user.model';
import { Router } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { CoachServiceImpl } from '../../services/impl/coach.service.impl';
import { PaginatorService } from '../../services/pagination.service';
import { EcoleServiceImpl } from '../../services/impl/ecole.service.impl';

@Component({
  selector: 'app-coach',
  imports: [FormsModule, ReactiveFormsModule, CommonModule],
  templateUrl: './coach.html',
  styleUrl: './coach.css'
})
export class Coach implements OnInit{
  coachForm:FormGroup;
  ecoleResponse?: RestResponse<EcoleModel[]>;
  coachResponse?: RestResponse<CoachModel[]>;
  coachRequest?: RestResponse<CoachModel>;
  keyword:string = '';
  ecole:number = 0;
  entity:number = 0;
  libelle:string = '';
  msg:string = '';
  ajout:boolean = false;
  error:boolean = false;
  user?:LogUser
  constructor(private router:Router, private paginatorService:PaginatorService, private formBuilder: FormBuilder, private authService:AuthServiceImpl, private coachService:CoachServiceImpl, private ecoleService:EcoleServiceImpl)
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

    this.filter()
  }

  modifEnt(id:any, kw:string = ''){
    console.log(id, kw)
    this.coachService.modifCoach(id).subscribe(
        response=>{
              //console.log(response.message)
              this.reloadPage(); 
            },        
        error => {
              console.error('Error sending data', error);
            })
  }

  addObj(){
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

  checkCorrect(){
    if(this.isFormEmpty(this.coachForm)){
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

  isFormEmpty(formGroup:FormGroup): boolean {
    for (const key in formGroup.controls) {
      if (formGroup.controls[key].invalid) {
        return true;
      }
    }
    return false;
  }

  changeEnt(val:any, l:any=''){
    this.entity = val;
    this.libelle = l
  }

  setMsg(msg:string = ''){
    this.msg = msg;
  }

  refresh(page:number=0,keyword:string="",ecole:number=0){
    this.coachService.findAllPg(page,keyword, ecole).subscribe(data=>this.coachResponse=data);
  }
  paginate(page:number){
    this.filter(page, this.keyword, this.ecole)
  }
  filter(page:number=0, keyword:string="", ecole:number=0){
    this.ecoleService.findAll().subscribe(data=>this.ecoleResponse=data);
    this.refresh(page,keyword,ecole)
    
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
