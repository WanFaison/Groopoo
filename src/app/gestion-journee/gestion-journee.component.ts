import { Component } from '@angular/core';
import Swal from 'sweetalert2'; 

@Component({
  selector: 'app-gestion-journee',
  templateUrl: './gestion-journee.component.html',
  styleUrls: ['./gestion-journee.component.css']
})
export class GestionJourneeComponent {

  // 🔹 Alerte pour Absence
  showAbsenceAlert() {
    Swal.fire({
      title: 'Liste des absences',
      html: `
        <p>Sélectionnez l'étudiant absent :</p>
        <select class="swal2-select">
          <option>Étudiant 1</option>
          <option>Étudiant 2</option>
          <option>Étudiant 3</option>
        </select>
      `,
      confirmButtonText: 'Valider',
      confirmButtonColor: '#104547',
      showCancelButton: true,
      cancelButtonText: 'Annuler',
    });
  }

  // 🔹 Alerte pour suppression
  showDeleteAlert() {
    Swal.fire({
      titleText: 'Êtes-vous sûr de vouloir supprimée cette journée ?',
      text: "NB: La suppression de cette journée entrainera également la suppression de toutes les absences.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Oui',
      cancelButtonText: 'Non'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire(
          'Supprimé !',
          'La journée a bien été supprimée.',
          'success'
        );
      }
    });
  }
}
