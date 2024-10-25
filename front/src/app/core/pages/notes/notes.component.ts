import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule } from '@angular/forms';
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

@Component({
  selector: 'app-notes',
  standalone: true,
  imports: [FormsModule, CommonModule, RouterModule, NavComponent, FootComponent],
  templateUrl: './notes.component.html',
  styleUrl: './notes.component.css'
})
export class NotesComponent implements OnInit{
  notesForm: FormGroup;
  grpList: GroupeModel[] = [];
  liste: number = 0;
  listeResponse?: RestResponse<ListeModel>;
  groupResponse?: RestResponse<GroupeModel[]>;
  user?:LogUser;
  constructor(private router:Router, private fb: FormBuilder, private groupeService:GroupeServiceImpl, private listeService:ListeServiceImpl, private authService:AuthServiceImpl){
    this.notesForm = this.fb.group({
      notes: this.fb.array([])
    })
  }

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/not-found'])
    }

    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
    }
    this.refresh(this.liste);

    this.notesForm = this.fb.group({
      notes: this.fb.array(this.notesArray())
    })

    this.notesForm.valueChanges.subscribe(value => {
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem('notesData', JSON.stringify(this.notesForm.value));
      }
    });
    console.log(this.notesForm.value);
  }

  notesArray(): FormGroup[] {
    return this.grpList.map(grp => this.fb.group({
      id: [grp.id],
      note: []
    }));
  }

  refresh(liste:number=0, page:number=0){
    this.groupeService.findAll(liste, page).subscribe(data=>this.groupResponse=data);
    if(this.groupResponse){this.grpList = this.groupResponse?.results}
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
