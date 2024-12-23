import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../nav/nav.component";
import { FootComponent } from "../foot/foot.component";
import { GroupsComponent } from "../../pages/groups/groups.component";
import { JoursComponent } from "../../pages/jours/jours.component";
import { NotesComponent } from "../../pages/notes/notes.component";
import { FormCoachComponent } from "../../pages/form-coach/form-coach.component";
import { LogUser } from '../../models/user.model';
import { AuthServiceImpl } from '../../services/impl/auth.service.impl';
import { FinalistComponent } from "../../pages/finalist/finalist.component";
import { JuryComponent } from "../../pages/jury/jury.component";
import { AttendanceComponent } from "../../pages/attendance/attendance.component";

@Component({
  selector: 'app-liste-menu',
  standalone: true,
  imports: [CommonModule, NavComponent, FootComponent, GroupsComponent, JoursComponent, NotesComponent, FormCoachComponent, FinalistComponent, JuryComponent, AttendanceComponent],
  templateUrl: './liste-menu.component.html',
  styleUrl: './liste-menu.component.css'
})
export class ListeMenuComponent implements OnInit{
  state:any = 0;
  user?:LogUser;
  constructor(private authService:AuthServiceImpl){}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    if(typeof window !== 'undefined' && localStorage){
      this.state = parseInt(localStorage.getItem('stateListeMenu') || '0', 10);
    }
  }

  changeState(num: number) {
    this.state = num;
    localStorage.setItem('stateListeMenu', this.state);
  }

}
