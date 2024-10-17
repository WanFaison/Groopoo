import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { FootComponent } from '../../components/foot/foot.component';
import { NavComponent } from '../../components/nav/nav.component';

@Component({
  selector: 'app-404',
  standalone: true,
  imports: [NavComponent, FootComponent, FormsModule, CommonModule, RouterLink, RouterLinkActive],
  templateUrl: './404.component.html',
  styleUrl: './404.component.css'
})
export class NotFoundComponent {

}
