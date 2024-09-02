import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../../components/nav/nav.component";
import { FootComponent } from '../../components/foot/foot.component';
import { RestResponse } from '../../models/rest.response';
import { ListeModel } from '../../models/liste.model';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavComponent, FootComponent, RouterLink, RouterLinkActive, CommonModule],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent implements OnInit{
  response?: RestResponse<ListeModel[]>;
  constructor(private listeService:ListeServiceImpl){}
  ngOnInit(): void {
    this.listeService.findAll().subscribe(data=>this.response=data);
    this.refresh()
  }

  refresh(page:number=0,keyword:string=""){
    this.listeService.findAll(page,keyword).subscribe(data=>this.response=data);
  }
  paginate(page:number){
    this.refresh(page)
  }
  searchLibelle(libelle:string){
    if(libelle.length>=3){
          this.refresh(0,libelle)
    }
  }

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }

}
