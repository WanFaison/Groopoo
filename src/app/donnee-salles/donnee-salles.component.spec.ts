import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DonneeSallesComponent } from './donnee-salles.component';

describe('DonneeSallesComponent', () => {
  let component: DonneeSallesComponent;
  let fixture: ComponentFixture<DonneeSallesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DonneeSallesComponent]
    });
    fixture = TestBed.createComponent(DonneeSallesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
