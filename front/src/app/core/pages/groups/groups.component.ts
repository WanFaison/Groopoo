import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';
import { RestResponse } from '../../models/rest.response';
import { GroupeModel } from '../../models/groupe.model';
import { GroupeServiceImpl } from '../../services/impl/groupe.service.impl';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { ListeModel } from '../../models/liste.model';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { EtudiantCreate, EtudiantCreateXlsx } from '../../models/etudiant.model';
import { EtudiantServiceImpl } from '../../services/impl/etudiant.service.impl';

@Component({
  selector: 'app-groups',
  standalone: true,
  imports: [NavComponent, FootComponent, RouterLink, RouterLinkActive, CommonModule, FormsModule],
  templateUrl: './groups.component.html',
  styleUrl: './groups.component.css'
})
export class GroupsComponent implements OnInit{
  liste: number = 0;
  listeResponse?: RestResponse<ListeModel>;
  etdResponse?: RestResponse<EtudiantCreateXlsx[]>;
  groupResponse?: RestResponse<GroupeModel[]>;
  constructor(private router:Router, private groupeService:GroupeServiceImpl, private listeService:ListeServiceImpl, private apiService:ApiService, private etudiantService:EtudiantServiceImpl) { }

  ngOnInit(): void {
    if (typeof window !== 'undefined' && localStorage){
      this.liste = parseInt(localStorage.getItem('newListe') || '1', 10);
      this.listeService.findById(this.liste).subscribe(data=>this.listeResponse=data);
    }
    this.refresh(this.liste);
    if (this.groupResponse) {
      console.log(this.groupResponse);
    } 
  }

  printXls(){
    this.apiService.getExcelSheet(this.liste).subscribe((data: Blob) => {
      const downloadUrl = window.URL.createObjectURL(data);
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = `${this.listeResponse?.results.libelle}.xlsx`;
      link.click();
    });
  }

  useListe(){
    this.etudiantService.findByListe(this.liste).subscribe(data=>this.etdResponse=data);
    console.log(this.etdResponse);

    if(this.etdResponse){
      localStorage.setItem('etudiants', JSON.stringify(this.etdResponse.results));
      this.clearData();
      this.router.navigate(['/app/liste-membre']);
    }
    
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

  clearData(){
    if (typeof window !== 'undefined' && localStorage){
      localStorage.removeItem('formData');
      localStorage.removeItem('ecoleListe');
      localStorage.removeItem('tailleGrp')
      localStorage.removeItem('nomGrp');
      localStorage.removeItem('anneeListe');
    }
    
  }

}
