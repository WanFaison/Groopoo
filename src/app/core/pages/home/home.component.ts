import { Component, OnInit } from '@angular/core';
import { NavComponent } from "../nav/nav.component";
import { FootComponent } from '../foot/foot.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavComponent, FootComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent implements OnInit{
  ngOnInit(): void {
  }

}
