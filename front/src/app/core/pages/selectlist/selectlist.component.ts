import { Component } from '@angular/core';
import { NavComponent } from '../nav/nav.component';
import { FootComponent } from '../foot/foot.component';

@Component({
  selector: 'app-selectlist',
  standalone: true,
  imports: [NavComponent, FootComponent],
  templateUrl: './selectlist.component.html',
  styleUrl: './selectlist.component.css'
})
export class SelectlistComponent {
  selectedFile: File | null = null;

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;

    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
      console.log('Fichier sélectionné:', this.selectedFile);
      // Vous pouvez maintenant traiter le fichier Excel ici
}
  }
onSubmit(): void {
  if (this.selectedFile) {
    // Traitez le fichier lors de la soumission du formulaire
    console.log('Téléversement du fichier:', this.selectedFile.name);
  } else {
    console.log('Aucun fichier sélectionné');
  }
}
}
