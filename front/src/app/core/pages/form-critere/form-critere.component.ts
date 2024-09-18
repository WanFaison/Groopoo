import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, FormArray, Validators, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { FootComponent } from "../../components/foot/foot.component";
import { NavComponent } from "../../components/nav/nav.component";
import { CommonModule } from '@angular/common';
import { Country, RestResponse } from '../../models/rest.response';
import { NiveauModel } from '../../models/niveau.model';
import { NiveauServiceImpl } from '../../services/impl/niveau.service.impl';
import { FiliereModel } from '../../models/filiere.model';
import { ClasseModel } from '../../models/classe.model';
import { FiliereServiceImpl } from '../../services/impl/filiere.service.impl';
import { ClasseServiceImpl } from '../../services/impl/classe.service.impl';
import { HttpClient } from '@angular/common/http';
import { RouterLink, RouterLinkActive } from '@angular/router';


@Component({
  selector: 'app-form-critere',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, FootComponent, NavComponent, CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './form-critere.component.html',
  styleUrl: './form-critere.component.css'
})
export class FormCritereComponent implements OnInit{
  nivResponse?: RestResponse<NiveauModel[]>;
  filResponse?: RestResponse<FiliereModel[]>;
  classeResponse?: RestResponse<ClasseModel[]>;
  nations: Country[] = [];
  form: FormGroup;
  niveau: any = [];
  filiere: any = [];
  classe: any = [];
  sexe: any = [];
  pays: any = [];
  ecole: number= 0;
  tailleGrp: number = 0;

  constructor(private http: HttpClient, private fb: FormBuilder, private niveauService:NiveauServiceImpl, private filiereService:FiliereServiceImpl, private classeService:ClasseServiceImpl) {
    this.form = this.fb.group({
      niveau: this.fb.array([]),
      filiere: this.fb.array([]),
      classe: this.fb.array([]),
      sexe: this.fb.array([]),
      pays: this.fb.array([])
    });
  }
  ngOnInit(): void {
    this.niveau = this.form.get('niveau') as FormArray;
    this.filiere = this.form.get('filiere') as FormArray;
    this.classe = this.form.get('classe') as FormArray;
    this.sexe = this.form.get('sexe') as FormArray;
    this.pays = this.form.get('pays') as FormArray;

    if (typeof window !== 'undefined' && localStorage.getItem('ecoleListe')){
      this.ecole = Number(localStorage.getItem('ecoleListe'));
      this.tailleGrp = Number(localStorage.getItem('tailleGrp'));
    }
    this.niveauService.findAll().subscribe(data=>this.nivResponse=data);
    this.filiereService.findAll(this.ecole).subscribe(data=>this.filResponse=data);
    this.classeService.findAll(this.ecole).subscribe(data=>this.classeResponse=data);
    this.loadCountries();
    this.loadForm();

    this.form.valueChanges.subscribe(value => {
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem('formData', JSON.stringify(this.form.value));
      }
    });
    
    console.log(this.form.value);
  }

  getRange() {
    return Array(this.tailleGrp).fill(0).map((_, i) => i + 1);
  }

  loadForm() {
    if (typeof window !== 'undefined' && localStorage.getItem('formData')){
        const savedData = localStorage.getItem('formData');
      if (savedData) {
        const parsedData = JSON.parse(savedData);
    
        this.niveau.clear();
        this.filiere.clear();
        this.classe.clear();
        this.sexe.clear();
    
        if (parsedData.niveau && Array.isArray(parsedData.niveau)) {
          parsedData.niveau.forEach((niveauItem: any) => {
            const pairGroup = this.fb.group({
              choix: [niveauItem.choix || '', Validators.required],
              taille: [niveauItem.taille || '', Validators.required],
            });
            this.niveau.push(pairGroup);
          });
        }
    
        if (parsedData.filiere && Array.isArray(parsedData.filiere)) {
          parsedData.filiere.forEach((filiereItem: any) => {
            const pairGroup = this.fb.group({
              choix: [filiereItem.choix || '', Validators.required],
              taille: [filiereItem.taille || '', Validators.required],
            });
            this.filiere.push(pairGroup);
          });
        }

        if (parsedData.classe && Array.isArray(parsedData.classe)) {
          parsedData.classe.forEach((classeItem: any) => {
            const pairGroup = this.fb.group({
              choix: [classeItem.choix || '', Validators.required],
              taille: [classeItem.taille || '', Validators.required],
            });
            this.classe.push(pairGroup);
          });
        }

        if (parsedData.sexe && Array.isArray(parsedData.sexe)) {
          parsedData.sexe.forEach((sexeItem: any) => {
            const pairGroup = this.fb.group({
              choix: [sexeItem.choix || '', Validators.required],
              taille: [sexeItem.taille || '', Validators.required],
            });
            this.sexe.push(pairGroup);
          });
        }

        if (parsedData.pays && Array.isArray(parsedData.pays)) {
          parsedData.pays.forEach((paysItem: any) => {
            const pairGroup = this.fb.group({
              choix: [paysItem.choix || '', Validators.required],
              taille: [paysItem.taille || '', Validators.required],
            });
            this.pays.push(pairGroup);
          });
        }
      }
    }
    
  }

  loadCountries(){
    this.http.get<any[]>('https://restcountries.com/v3.1/all').subscribe(
      (countries) => {
        this.nations = countries.map((country) => ({
          nom: country.name.common,
          code: country.cca3,
        }))
        .sort((a, b) => a.nom.localeCompare(b.nom));
      },
      (error) => {
        console.error('Error fetching countries:', error);
      }
    );
  }

  initFormArray(formArray: FormArray, count: number): void {
    const pairGroup = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
    });
    while (formArray.length < count) {
      formArray.push(pairGroup);
    }
  }

  get niveauArray() {
    return this.form.get('niveau') as FormArray;
  }
  get filiereArray() {
    return this.form.get('filiere') as FormArray;
  }

  initPairs(){
    const pairGroup = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
    });
    
    this.niveau.push(pairGroup);
    this.filiere.push(pairGroup);
    this.classe.push(pairGroup);
    this.sexe.push(pairGroup);
    this.pays.push(pairGroup);
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
    }else if(type =="classe"){
      this.classe.push(pairGroup);
    }else if(type =="sexe"){
      this.sexe.push(pairGroup);
    }else{
      this.pays.push(pairGroup);
    }
    console.log(this.form.value);
  }

  removePair(type:string, index:number) {
    if (type =="niveau") {
      this.niveau.removeAt(index);
    }else if(type =="filiere"){
      this.filiere.removeAt(index);
    }
    else if(type =="classe"){
      this.classe.removeAt(index);
    }
    else if(type =="sexe"){
      this.sexe.removeAt(index);
    }else{
      this.pays.removeAt(index);
    }
  }

  onSubmit() {
    console.log(this.form.value);
  }

}
