import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { EtudiantCreateXlsx } from '../../models/etudiant.model';
import { GroupeJourModel, GroupeModel, GroupeReqModel } from '../../models/groupe.model';
import { ListeModel } from '../../models/liste.model';
import { FindRequestResponse, RestResponse } from '../../models/rest.response';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { GroupeServiceImpl } from '../../services/impl/groupe.service.impl';
import { AbsenceImplService } from '../../services/impl/absence.service.impl';
import { response } from 'express';
import { JourModel } from '../../models/jour.model';
import { JourServiceImpl } from '../../services/impl/jour.service.impl';

@Component({
  selector: 'app-attendance',
  standalone: true,
  imports: [FormsModule, ReactiveFormsModule, CommonModule, RouterModule, NavComponent, FootComponent],
  templateUrl: './attendance.component.html',
  styleUrl: './attendance.component.css'
})
export class AttendanceComponent implements OnInit{
  liste: number = 0;
  grp:number = 0;
  groupResponse?: RestResponse<GroupeJourModel[]>;
  listeResponse?: RestResponse<ListeModel>;
  grpReq?:RestResponse<GroupeReqModel[]>
  jourResponse?:FindRequestResponse<JourModel>
  user?:LogUser;
  jour:any = 0
  msg:string = ''
  attendanceForm: { id: number; emargement1: boolean; emargement2: boolean }[] = [];
  constructor(private router:Router, private jourService:JourServiceImpl, private absenceService:AbsenceImplService, private fb: FormBuilder, private groupeService:GroupeServiceImpl, private listeService:ListeServiceImpl, private authService:AuthServiceImpl)
  {}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/not-found'])
    }

    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.jour = parseInt(localStorage.getItem('jrListe') || '1', 10);
      this.jourService.findJour(this.jour).subscribe(data=>this.jourResponse=data)
      this.groupeService.findAllReq(this.liste).subscribe(data=>this.grpReq=data)
      this.groupeService.findByJour(this.jour).subscribe(data=>this.groupResponse=data)
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
      this.refresh(this.jour);
    }
  }

  onAttendanceChange(etdId: number, emargement: number, event:Event): void {
    const checked = (event.target as HTMLInputElement).checked;
    const existingEntry = this.attendanceForm.find(entry => entry.id === etdId);
    
    if (existingEntry) {
      if (emargement === 1) {
        existingEntry.emargement1 = checked;
      } else {
        existingEntry.emargement2 = checked;
      }
    } else {
      this.attendanceForm.push({
        id: etdId,
        emargement1: emargement === 1 ? checked : true,
        emargement2: emargement === 2 ? checked : true
      });
    }
    
    localStorage.setItem('attendanceForm', JSON.stringify(this.attendanceForm))
    console.log(this.attendanceForm);
  }

  setAbsences() {
    if (typeof window !== 'undefined' && localStorage){
      const absences = localStorage.getItem('attendanceForm')
      if(absences){
        const data = {
          jour: this.jour,
          absences: absences? JSON.parse(absences):[]
        }
  
        this.absenceService.sendAbsences(data).subscribe(
          response => {
            console.log(response)
            alert('Les absences ont été enregistrées')
            localStorage.removeItem('attendanceForm')
          },
          error => {
            localStorage.removeItem('attendanceForm')
            console.error('Error sending data', error);
          }
        )
        this.router.navigate(['/app/view-jours'])
      }else{
        this.msg = 'Aucune absence enregistrée'
      }
    }
    
  }

  refresh(jour:number=0, page:number=0, limit:number = 1, groupe:number=0){
    this.groupeService.findByJour(jour, page, limit, groupe)
                      .subscribe(data=>{this.groupResponse=data});
  }
  paginate(page:number){
    this.refresh(this.jour, page)
  }
  filter() {
    this.refresh(this.jour, 0, 1, this.grp)
  }

  reloadPage() {
    window.location.reload();
  }
}
