import { Component, OnInit } from '@angular/core';
import { RestResponse } from '../../models/rest.response';
import { JuryFinalModel, JuryModel } from '../../models/jury.model';
import { CoachModel } from '../../models/coach.model';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { CoachServiceImpl } from '../../services/impl/coach.service.impl';
import { JuryServiceImpl } from '../../services/impl/jury.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { ListeModel } from '../../models/liste.model';
import { FootComponent } from "../../components/foot/foot.component";
import { NavComponent } from "../../components/nav/nav.component";
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PaginatorService } from '../../services/pagination.service';
import { GroupeFinalModel, GroupeReqModel } from '../../models/groupe.model';
import { GroupeServiceImpl } from '../../services/impl/groupe.service.impl';

@Component({
    selector: 'app-finalist',
    imports: [CommonModule, FormsModule],
    templateUrl: './finalist.component.html',
    styleUrl: './finalist.component.css'
})
export class FinalistComponent implements OnInit{
  juryResponse?:RestResponse<JuryFinalModel[]>;
  coachRequest?:RestResponse<CoachModel[]>;
  listeResponse?:RestResponse<ListeModel>;
  groupResponse?:RestResponse<GroupeFinalModel[]>;
  liste:number = 0;
  user?:LogUser;
  juryName:string = '';
  mode:string = '100';
  newJury:number = 0;
  newCoach:number = 0;

  constructor(private juryService:JuryServiceImpl, private groupService:GroupeServiceImpl, private paginatorService:PaginatorService, private authService:AuthServiceImpl, private listeService:ListeServiceImpl, private coachService:CoachServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
      this.refresh()
    }
  }

  printJuryXls() {
    this.juryService.getFinalJurySheet(this.liste).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = `${this.listeResponse?.results.libelle} - Jury Finalistes.xlsx`;
      link.click();
    });
  }

  printFinalistes(){
    this.groupService.getFinalisteSheet(this.liste, this.mode, 2).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = `${this.listeResponse?.results.libelle} - Finalistes.xlsx`;
      link.click();
    });
  }

  findCoachesLeft(jury: JuryModel) {
    this.newJury = jury.id;
    this.juryName = jury.libelle;
    this.coachService.findAllNotInListe(this.liste, 1).subscribe(data=>this.coachRequest=data);
  }

  extractNumber(input: string): string {
    const match = input.match(/\d+/);
    return match ? match[0] : ''; 
  }

  transferCoach(coachId: number|undefined, juryId: number) {
    if(coachId){
      this.coachService.transferCoach(coachId, juryId).subscribe(
        response=>{
          if(response.data == 0){ this.reloadPage(); }
        },        
        error => {console.error('Error sending data', error);});
    }
  }

  retirerCoach(coachId:number, juryId:number){
    this.juryService.removeCoach(coachId, juryId).subscribe(
      response=>{
        if(response.data == 0){ this.reloadPage(); }
      },        
      error => { console.error('Error sending data', error); });
  }

  refresh(mode:string = this.mode, liste:number = this.liste){
    mode? this.mode = mode: null;
    this.juryService.finalJury(liste).subscribe(data=>this.juryResponse=data);
    if(mode == '100'){
      this.groupService.findTop100(liste).subscribe(data=>this.groupResponse=data);
    }else{
      this.groupService.findTop2PerCoach(liste).subscribe(data=>this.groupResponse=data);
    }
  }

  reloadPage() {
    return this.paginatorService.reloadPage();
  }

}
