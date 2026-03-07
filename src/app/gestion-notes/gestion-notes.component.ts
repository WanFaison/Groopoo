import { Component } from '@angular/core';

@Component({
  selector: 'app-gestion-notes',
  templateUrl: './gestion-notes.component.html',
  styleUrls: ['./gestion-notes.component.css']
})
export class GestionNotesComponent {

  validerEnregistrement() {
    console.log("Enregistrement validé !");
   
  }
  
}
