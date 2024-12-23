import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { RequestResponse, RestResponse } from '../../models/rest.response';
import { JuryModel } from '../../models/jury.model';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { CoachServiceImpl } from '../../services/impl/coach.service.impl';
import { ListeModel } from '../../models/liste.model';
import { JuryServiceImpl } from '../../services/impl/jury.service.impl';
import { GroupeReqModel } from '../../models/groupe.model';
import { CoachModel } from '../../models/coach.model';
import { response } from 'express';
import { PaginatorService } from '../../services/pagination.service';

@Component({
  selector: 'app-jury',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './jury.component.html',
  styleUrl: './jury.component.css'
})
export class JuryComponent implements OnInit{
  juryResponse?:RestResponse<JuryModel[]>;
  juryRequest?: RestResponse<JuryModel[]>;
  listeResponse?: RestResponse<ListeModel>;
  coachResponse?:RestResponse<CoachModel>;
  coachRequest?:RestResponse<CoachModel[]>;
  liste:number = 0;
  keyword:string = '';
  groups?:GroupeReqModel[];
  juryName:string = '';
  newJury:number = 0;
  newCoach:number = 0;
  user?:LogUser;
  constructor(private router:Router, private paginatorService:PaginatorService, private juryService:JuryServiceImpl, private authService:AuthServiceImpl, private listeService:ListeServiceImpl, private coachService:CoachServiceImpl){}
  
  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/liste-menu'])
    }

    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
      this.refresh()
    }
  }

  findCoach(id: number) {
    this.coachService.findById(id).subscribe(data=>this.coachResponse=data);
    this.coachTransfer(id);
  }
  findCoachesLeft(jury:JuryModel){
    this.juryName = jury.libelle;
    this.newJury = jury.id;
    this.coachService.findAllNotInListe(this.liste).subscribe(data=>this.coachRequest=data);
  }

  showJuryGroups(groups:GroupeReqModel[], juryName:string=''){
    this.juryName = juryName
    this.groups = groups
  }

  coachTransfer(coachId:number){
    this.juryService.findAllButOne(this.liste, coachId).subscribe(data=>this.juryRequest=data);
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

  extractNumber(input: string): string {
    const match = input.match(/\d+/);
    return match ? match[0] : ''; 
  }

  printJuryXls(motif:string = ''){
    this.juryService.getJurySheet(this.liste, motif).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = `${this.listeResponse?.results.libelle} - Repartition des Jury.xlsx`;
      link.click();
    });
  }

  refresh(page:number=0, liste:number=this.liste, limit:number=10, keyword:string=''){
    this.juryService.findAllPg(page, liste, limit, keyword).subscribe(data => {
      this.juryResponse = data;
      //console.log(this.juryResponse);
    });
  }
  filter(){
    this.refresh(0, this.liste, 10, this.keyword)
  }
  paginate(page:number){
    this.refresh(page, this.liste)
  }

  getPageRange(currentPage:any, totalPages:any): number[] {
    return this.paginatorService.getPageRange(currentPage, totalPages)
  }

  reloadPage() {
    return this.paginatorService.reloadPage();
  }

}
