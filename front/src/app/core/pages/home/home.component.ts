import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../../components/nav/nav.component";
import { FootComponent } from '../../components/foot/foot.component';
import { RestResponse } from '../../models/rest.response';
import { ListeModel } from '../../models/liste.model';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AnneeModel } from '../../models/annee.model';
import { AnneeServiceImpl } from '../../services/impl/annee.service.impl';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavComponent, FootComponent, RouterLink, RouterLinkActive, CommonModule, FormsModule],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent implements OnInit{
  response?: RestResponse<ListeModel[]>;
  anneeResponse?: RestResponse<AnneeModel[]>;
  keyword: string = '';
  annee: number = 0;
  constructor(private listeService:ListeServiceImpl, private anneeService:AnneeServiceImpl){}
  
  ngOnInit(): void {
    this.anneeService.findAll().subscribe(data=>this.anneeResponse=data);
    this.filter()
  }

  refresh(page:number=0,keyword:string=""){
    this.listeService.findAll(page,keyword).subscribe(data=>this.response=data);
  }
  paginate(page:number){
    this.refresh(page)
  }
  filter(page:number=0, keyword:string=this.keyword, annee:number=0){
    this.listeService.findAll(page,keyword,annee).subscribe(data=>this.response=data);
  }

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }

}
