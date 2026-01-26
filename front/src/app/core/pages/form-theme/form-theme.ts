import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ThemeServiceImpl } from '../../services/impl/theme.service.impl';
import { Router } from '@angular/router';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { PaginatorService } from '../../services/pagination.service';
import { ListeModel } from '../../models/liste.model';
import { RestResponse } from '../../models/rest.response';
import { LogUser } from '../../models/user.model';
import { ThemeModel } from '../../models/theme.model';

@Component({
  selector: 'app-form-theme',
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './form-theme.html',
  styleUrl: './form-theme.css'
})
export class FormTheme implements OnInit{
  themeForm: {id: number; add: boolean; libelle: string}[] = [];
  themeResponse?:RestResponse<ThemeModel[]>;
  themeActiveResponse?:RestResponse<ThemeModel[]>;
  listeResponse?: RestResponse<ListeModel>;
  user?:LogUser;
  liste: number = 0;
  cnt: number = 0;
  listeCnt: number = 0;
  keyword:string = '';
  msg:string = '';
  constructor(private paginatorService:PaginatorService, private router:Router, private authService:AuthServiceImpl, private listeService:ListeServiceImpl, private formBuilder: FormBuilder, private themeService: ThemeServiceImpl){}

  ngOnInit(): void {
    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>{
        this.listeResponse=data;
        this.listeCnt = data.results.count;

        if(this.themeForm.length<1){
          this.themeService.findByListe(this.liste).subscribe(data=>{
            this.themeActiveResponse=data;
            this.loadActiveThemes()
            localStorage.setItem('themeForm', JSON.stringify(this.themeForm));
          });
        }
        
      });
    }
    const sform = localStorage.getItem('themeForm')
    this.themeForm = sform? JSON.parse(sform) : []; 
    this.refresh()
  }

  loadActiveThemes(){
    this.themeActiveResponse?.results.forEach(item => {
      if(!this.checkAdded(item.id)){
        this.themeForm.push({
        id: item.id,
        add: true,
        libelle: item.libelle
      })}
    });
  }

  checkAdded(themeId: number){
    const existingEntry = this.themeForm.find(entry => entry.id === themeId);
    if (existingEntry) {return existingEntry.add}
    return false;
  }

  changeState(num: any) {
    localStorage.setItem('stateListeMenu', num);
    this.reloadPage()
  }

  onFormChange(themeId: number, themeLibelle: string, event:Event): void {
    const checked = (event.target as HTMLInputElement).checked;
    const existingEntry = this.themeForm.find(entry => entry.id === themeId);
    
    if (existingEntry) {
      existingEntry.add = checked;
    } else {
      this.themeForm.push({
        id: themeId,
        add: checked,
        libelle: themeLibelle
      });
    }
    localStorage.setItem('themeForm', JSON.stringify(this.themeForm))
    console.log(this.themeForm);
  }

  assignThemes() {
    if (typeof window !== 'undefined' && localStorage){
      const themes = localStorage.getItem('themeForm')
      if(themes && JSON.parse(themes).length>0){
        const data = {
          liste: this.liste,
          themes: themes? this.themeForm.filter(theme => theme.add === true):[], 
        }
  
        this.themeService.assignThemes(data).subscribe(
          response => {
            console.log(response)
            if(response.data != 0){
              this.msg = "Erreur! Vous essayez d'affecter plus de themes que de groupes disponibles"
            }else{
              localStorage.removeItem('themeForm');
              this.changeState(0);
            }
          },
          error => {
            localStorage.removeItem('themeForm');
            console.error('Error sending data', error);
          }
        )
      }else{
        this.msg = 'Aucun theme assigné'
      }
    }
    
  }

  refresh(page:number=0, keyword:string=''){
    this.themeService.findAllPg(page, keyword)
                      .subscribe(data=>{this.themeResponse=data});
  }
  filter(page:number=0, keyword:string=""){
    this.refresh(page,keyword)
  }
  paginate(page:number){
    this.refresh(page, this.keyword)
  }

  reloadPage(){
    this.paginatorService.reloadPage();
  }

}
