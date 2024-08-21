import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../nav/nav.component";
import { FootComponent } from "../foot/foot.component";

@Component({
  selector: 'app-membres',
  standalone: true,
  imports: [NavComponent, FootComponent],
  templateUrl: './membres.component.html',
  styleUrl: './membres.component.css'
})
export class MembresComponent implements OnInit{
  ngOnInit(): void {
  }

}
