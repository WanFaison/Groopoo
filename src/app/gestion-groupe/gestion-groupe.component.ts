import { Component } from '@angular/core';

@Component({
  selector: 'app-gestion-groupe',
  templateUrl: './gestion-groupe.component.html',
  styleUrls: ['./gestion-groupe.component.css']
})
export class GestionGroupeComponent {
  groups = Array.from({ length: 20 }, (_, i) => i + 1);

}
