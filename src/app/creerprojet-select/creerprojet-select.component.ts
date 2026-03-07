import { Component } from '@angular/core';

@Component({
  selector: 'app-creerprojet-select',
  templateUrl: './creerprojet-select.component.html',
  styleUrls: ['./creerprojet-select.component.css']
})
export class CreerprojetSelectComponent {

  afficherInputs = false;

  afficherSection() {
    this.afficherInputs = true;
  }

  options = [
    { value: 'classe', label: 'Grouper par classe', icon: 'bi bi-people' },
    { value: 'aleatoire', label: 'Groupes aléatoires', icon: 'bi bi-shuffle' },
    { value: 'sexe', label: 'Grouper par sexe', icon: 'bi bi-gender-ambiguous' },
    { value: 'critere', label: 'Définir critère par groupe', icon: 'bi bi-funnel' }
  ];
  
  selectedOption = '';
  
  selectOption(value: string) {
    this.selectedOption = value;
  }

  
  

}
