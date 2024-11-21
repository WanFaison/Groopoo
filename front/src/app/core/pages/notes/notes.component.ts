import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { GroupeModel } from '../../models/groupe.model';
import { ListeModel } from '../../models/liste.model';
import { RestResponse } from '../../models/rest.response';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { GroupeServiceImpl } from '../../services/impl/groupe.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { response } from 'express';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-notes',
  standalone: true,
  imports: [FormsModule, ReactiveFormsModule, CommonModule, RouterModule, NavComponent, FootComponent],
  templateUrl: './notes.component.html',
  styleUrl: './notes.component.css'
})
export class NotesComponent implements OnInit{
  notesForm: {id:number; note:number}[] = [];
  liste: number = 0;
  listeResponse?: RestResponse<ListeModel>;
  groupResponse?: RestResponse<GroupeModel[]>;
  user?:LogUser;
  msg:string = ''
  constructor(private router:Router, private apiService:ApiService, private fb: FormBuilder, private groupeService:GroupeServiceImpl, private listeService:ListeServiceImpl, private authService:AuthServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/not-found'])
    }
    alert('Avant d’attribuer des notes aux différents groupes, assurez-vous que toutes les absences ont été correctement enregistrées.')
    
    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
      this.refresh(this.liste);
    }  
  }

  onNoteChange(grpId:number, event:Event){
    const inputElement = event.target as HTMLInputElement;
    const newNote = parseFloat(inputElement.value);
    const noteIndex = this.notesForm.findIndex(note => note.id === grpId);

    if (noteIndex > -1) {
      this.notesForm[noteIndex].note = newNote;
    } else {
      this.notesForm.push({ id: grpId, note: newNote });
    }

    localStorage.setItem('notesForm', JSON.stringify(this.notesForm))
    console.log(this.notesForm);
  }

  onSubmit() {
    if (typeof window !== 'undefined' && localStorage){
      const notes = localStorage.getItem('notesForm')
      if(notes){
        const data = {
          notes: notes? JSON.parse(notes):[]
        }

        this.listeService.setNotes(data).subscribe(
          response =>{
            console.log(response)
            alert('Les notes ont été comptabilisées')
            localStorage.removeItem('notesForm')
          },
          error => {
            localStorage.removeItem('notesForm')
            console.error('Error sending data', error);
          }
        )
      }else{
        this.msg = 'Aucune note modifiée'
      }
    }
  }

  printXls(motif:string = ''){
    this.apiService.getExcelSheet(this.liste, motif).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = `${this.listeResponse?.results.libelle} Resultats.xlsx`;
      link.click();
    });
  }

  archiverListe(liste:any, motif:string='archive'){
    this.listeService.modifListe(liste, motif).subscribe(
      response=>{
            console.log(response.message)
            this.reloadPage(); 
          },        
      error => {
            console.error('Error sending data', error);
          })
  }

  refresh(liste:number=0, page:number=0){
    this.groupeService.findAll(liste, page).subscribe(data=>this.groupResponse=data);
  }
  paginate(page:number){
    this.refresh(this.liste, page)
  }

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }

  reloadPage() {
    window.location.reload();
  }

}
