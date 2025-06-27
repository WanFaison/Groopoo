import { Component, OnInit } from '@angular/core';

@Component({
    selector: 'app-foot',
    imports: [],
    templateUrl: './foot.component.html',
    styleUrl: './foot.component.css'
})
export class FootComponent implements OnInit{
  ngOnInit(): void {
  }

  getCurrentYear(): number {
    const today = new Date();
    return today.getFullYear();
  }

}
