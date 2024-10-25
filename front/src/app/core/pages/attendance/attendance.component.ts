import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { EtudiantCreateXlsx } from '../../models/etudiant.model';
import { GroupeModel } from '../../models/groupe.model';
import { ListeModel } from '../../models/liste.model';
import { RestResponse } from '../../models/rest.response';
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { GroupeServiceImpl } from '../../services/impl/groupe.service.impl';

@Component({
  selector: 'app-attendance',
  standalone: true,
  imports: [FormsModule, CommonModule, RouterModule, NavComponent, FootComponent],
  templateUrl: './attendance.component.html',
  styleUrl: './attendance.component.css'
})
export class AttendanceComponent implements OnInit{
  liste: number = 0;
  listeResponse?: RestResponse<ListeModel>;
  groupResponse?: RestResponse<GroupeModel[]>;
  user?:LogUser;
  jour:any = 0
  constructor(private router:Router, private groupeService:GroupeServiceImpl, private listeService:ListeServiceImpl, private authService:AuthServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(this.user?.role == 'ROLE_VISITEUR'){
      this.router.navigate(['/app/not-found'])
    }

    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.jour = parseInt(localStorage.getItem('jrListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
    }
    this.refresh(this.liste);
  }

  setAbsences() {
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
