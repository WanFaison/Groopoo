import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../../components/nav/nav.component";
import { FootComponent } from "../../components/foot/foot.component";
import * as XLSX from 'xlsx';
import { EtudiantCreate, EtudiantModel } from '../../models/etudiant.model';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';


@Component({
  selector: 'app-membres',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, CommonModule, RouterLink],
  templateUrl: './membres.component.html',
  styleUrl: './membres.component.css'
})
export class MembresComponent implements OnInit{
  fileName: string = '';
  etudiants: EtudiantCreate[] = [];

  ngOnInit(): void {
  }

  importExcel(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.fileName = file.name;

      const reader = new FileReader();
      reader.onload = (e: any) => {
        const arrayBuffer = e.target.result;
        const workbook = XLSX.read(arrayBuffer, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const json = XLSX.utils.sheet_to_json<EtudiantCreate>(workbook.Sheets[sheetName]);

        this.etudiants = json;
        console.log(this.etudiants); //show on console
      };
      reader.readAsArrayBuffer(file);
    }
  }



}
