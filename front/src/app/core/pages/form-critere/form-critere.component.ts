import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, FormArray, Validators, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { FootComponent } from "../../components/foot/foot.component";
import { NavComponent } from "../../components/nav/nav.component";
import { CommonModule } from '@angular/common';
import { RestResponse } from '../../models/rest.response';
import { NiveauModel } from '../../models/niveau.model';
import { NiveauServiceImpl } from '../../services/impl/niveau.service.impl';

@Component({
  selector: 'app-form-critere',
  standalone: true,
  imports: [FootComponent, NavComponent, CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './form-critere.component.html',
  styleUrl: './form-critere.component.css'
})
export class FormCritereComponent implements OnInit{
  response?: RestResponse<NiveauModel[]>;
  form: FormGroup;
  niveau: any = [];
  filiere: any = [];

  constructor(private fb: FormBuilder, private niveauService:NiveauServiceImpl) {
    this.form = this.fb.group({
      niveau: this.fb.array([]),
      filiere: this.fb.array([]),
    });
  }
  ngOnInit(): void {
    this.niveau = this.form.get('niveau') as FormArray;
    this.filiere = this.form.get('filiere') as FormArray;
    this.niveauService.findAll().subscribe(data=>this.response=data);
    this.initPairs();
    console.log(this.form.value);
  }

  get pairs() {
    return this.form.get('niveau') as FormArray;
  }

  initPairs(){
    const pairGroup = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
    });
    
    this.niveau.push(pairGroup);
    this.filiere.push(pairGroup);
  }

  addPair(type:string) {
    const pairGroup = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
    });
    
    if (type =="niveau") {
      this.niveau.push(pairGroup);
    }else if(type =="filiere"){
      this.filiere.push(pairGroup);
    }
  }

  removePair(type:string, index:number) {
    if (type =="niveau") {
      this.niveau.removeAt(index);
    }else if(type =="filiere"){
      this.filiere.removeAt(index);
    }
  }

  onSubmit() {
    console.log(this.form.value);
    // Handle form submission
  }

}
