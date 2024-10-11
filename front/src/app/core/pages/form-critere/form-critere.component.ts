import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, FormArray, Validators, FormsModule, ReactiveFormsModule, Form } from '@angular/forms';
import { FootComponent } from "../../components/foot/foot.component";
import { NavComponent } from "../../components/nav/nav.component";
import { CommonModule } from '@angular/common';
import { RestResponse } from '../../models/rest.response';
import { NiveauModel } from '../../models/niveau.model';
import { NiveauServiceImpl } from '../../services/impl/niveau.service.impl';
import { FiliereModel } from '../../models/filiere.model';
import { ClasseModel } from '../../models/classe.model';
import { FiliereServiceImpl } from '../../services/impl/filiere.service.impl';
import { ClasseServiceImpl } from '../../services/impl/classe.service.impl';
import { HttpClient } from '@angular/common/http';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { parse } from 'path';


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
  form: FormGroup;
  niveau: any = [];
  filiere: any = [];
  classe: any = [];
  ecole: number= 0;
  tailleGrp: number = 0;
  numNiv: number = 0;

  constructor(private http: HttpClient, private fb: FormBuilder, private niveauService:NiveauServiceImpl, private filiereService:FiliereServiceImpl, private classeService:ClasseServiceImpl) {
    this.form = this.fb.group({
      niveau: this.fb.array([]),
      filiere: this.fb.array([]),
      classe: this.fb.array([]),
    });
  }
  ngOnInit(): void {
    this.niveau = this.form.get('niveau') as FormArray;
    this.filiere = this.form.get('filiere') as FormArray;
    this.classe = this.form.get('classe') as FormArray;

    if (typeof window !== 'undefined' && localStorage.getItem('ecoleListe')){
      this.ecole = Number(localStorage.getItem('ecoleListe'));
      this.tailleGrp = Number(localStorage.getItem('tailleGrp'));
    }
    this.niveauService.findAll().subscribe(data=>this.nivResponse=data);
    this.filiereService.findAll(this.ecole).subscribe(data=>this.filResponse=data);
    this.classeService.findAll(this.ecole).subscribe(data=>this.classeResponse=data);
    if (this.nivResponse?.results) { this.numNiv = this.nivResponse?.results.length }
    this.loadForm();

    this.form.valueChanges.subscribe(value => {
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem('formData', JSON.stringify(this.form.value));
      }
    });
    
    console.log(this.form.value);
  }

  getRange(type:string = 'type', index:any = 1, num: number = this.tailleGrp) {
    let usedCapacity = 0;
    let remainingCapacity = 0;

    if(type == 'filiere'){
      usedCapacity = this.filiere.controls
        .filter((_: any, i: any) => i !== index)
        .reduce((sum: number, control: { get: (arg0: string) => { (): any; new(): any; value: any; }; }) => sum + Number(control.get('taille')?.value || 0), 0);
    }else if(type == 'classe'){
      usedCapacity = this.classe.controls
        .filter((_: any, i: any) => i !== index)
        .reduce((sum: number, control: { get: (arg0: string) => { (): any; new(): any; value: any; }; }) => sum + Number(control.get('taille')?.value || 0), 0);
    }else if(type == 'niveau'){
      usedCapacity = this.niveau.controls
        .filter((_: any, i: any) => i !== index)
        .reduce((sum: number, control: { get: (arg0: string) => { (): any; new(): any; value: any; }; }) => sum + Number(control.get('taille')?.value || 0), 0);
    }
    
    if(type == 'niv'){
      return Array.from({ length: num + 1 }, (_, i) => i).slice(1)
    }else{
      remainingCapacity = num - usedCapacity;
      const range = Array.from({ length: remainingCapacity + 1 }, (_, i) => i).slice(1)
      return range;
    }
  }
  getTailleFiliere(pair: FormGroup, index:any) {
    const taille = pair.get('taille')?.value || 0;
    const niveauArray = pair.get('niveau') as FormArray;
    const usedTaille = niveauArray.controls
      .filter((_: any, i: any) => i !== index)
      .map(control => control.get('taille')?.value)
      .reduce((sum, value) => sum + (parseInt(value, 10) || 0), 0);

    return Math.max(0, taille - usedTaille);
  }

  loadForm() {
    if (typeof window !== 'undefined' && localStorage.getItem('formData')){
        const savedData = localStorage.getItem('formData');
      if (savedData) {
        const parsedData = JSON.parse(savedData);
    
        this.niveau.clear();
        this.filiere.clear();
        this.classe.clear();
    
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
            const niveauArray:FormArray = this.fb.array([]);
        
            if (filiereItem.niveau && Array.isArray(filiereItem.niveau)) {
              filiereItem.niveau.forEach((fn: any) => {
                const nivPair = this.fb.group({
                  choix: [fn.choix || '', Validators.required],
                  taille: [fn.taille || '', Validators.required],
                });
                niveauArray.push(nivPair); 
              });
            }
        
            const pairGroup = this.fb.group({
              choix: [filiereItem.choix || '', Validators.required],
              taille: [filiereItem.taille || '', Validators.required],
              niveau: niveauArray
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
      }
    }
    
  }

  // loadCountries(){
  //   this.http.get<any[]>('https://restcountries.com/v3.1/all').subscribe(
  //     (countries) => {
  //       this.nations = countries.map((country) => ({
  //         nom: country.name.common,
  //         code: country.cca3,
  //       }))
  //       .sort((a, b) => a.nom.localeCompare(b.nom));
  //     },
  //     (error) => {
  //       console.error('Error fetching countries:', error);
  //     }
  //   );
  // }

  initFormArray(formArray: FormArray, count: number): void {
    const pairGroup = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
    });
    while (formArray.length < count) {
      formArray.push(pairGroup);
    }
  }

  addPair(type:string) {
    const pairGroup = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
    });
    const triGroup = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
      niveau: this.fb.array([])
    });
    
    if (type =="niveau") {
      this.niveau.push(pairGroup);
    }else if(type =="filiere"){
      this.filiere.push(triGroup);
    }else if(type =="classe"){
      this.classe.push(pairGroup);
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
  }

  addNivPair(index:number, method:string = '+'){
    const filiereArray = this.form.get('filiere') as FormArray;
    const filiereGroup = filiereArray.at(index) as FormGroup;
    let niveauArray = filiereGroup.get('niveau') as FormArray;
    if (!niveauArray) {
      niveauArray = this.fb.array([]);
      filiereGroup.setControl('niveau', niveauArray);
    }

    const nivPair = this.fb.group({
      choix: ['', Validators.required],
      taille: ['', Validators.required],
    });
    if((niveauArray.length == 0) && (method == 'change')){
      niveauArray.push(nivPair);
    }else if(method == '+'){
      niveauArray.push(nivPair)
    }
  }

  removeNivPair(i:number, j:number){
    const filiereArray = this.form.get('filiere') as FormArray;
    const filiereGroup = filiereArray.at(i) as FormGroup;
    let niveauArray = filiereGroup.get('niveau') as FormArray;
    niveauArray.removeAt(j);
  }

  onSubmit() {
    console.log(this.form.value);
  }

}
