import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../../components/nav/nav.component";
import { FootComponent } from '../../components/foot/foot.component';
import { RestResponse } from '../../models/rest.response';
import { ListeModel } from '../../models/liste.model';
import { ListeServiceImpl } from '../../services/impl/list.service.impl';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavComponent, FootComponent, RouterLink, RouterLinkActive, CommonModule, FormsModule],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent implements OnInit{
  response?: RestResponse<ListeModel[]>;
  keyword: string = '';
  startDate: string = '';
  endDate: string = '';
  constructor(private listeService:ListeServiceImpl){}
  ngOnInit(): void {
    this.filter()
  }

  refresh(page:number=0,keyword:string=""){
    this.listeService.findAll(page,keyword).subscribe(data=>this.response=data);
  }
  paginate(page:number){
    this.refresh(page)
  }
  filter(page:number=0, keyword:string=this.keyword, startDate:string='', endDate:string=this.endDate){
    this.listeService.findAll(page,keyword,startDate,endDate).subscribe(data=>this.response=data);
  }

  pages(start: number, end: number | undefined = 5): number[] {
    return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
  }

}
