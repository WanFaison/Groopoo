import { Component } from '@angular/core';

@Component({
  selector: 'app-projet-selection',
  templateUrl: './projet-selection.component.html',
  styleUrls: ['./projet-selection.component.css']
})
export class ProjetSelectionComponent {

  
  showFiliere = false;
  showClasse = false;
  showNiveau = false;
  showSexe = false;

  toggleFiliere() {
    this.showFiliere = !this.showFiliere;
  }
  toggleClasse() {
    this.showClasse = !this.showClasse;
  }
  toggleNiveau() {
    this.showNiveau = !this.showNiveau;
  }
  toggleSexe() {
    this.showSexe = !this.showSexe;
  }
}
