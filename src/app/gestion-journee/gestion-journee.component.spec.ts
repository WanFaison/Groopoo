import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GestionJourneeComponent } from './gestion-journee.component';

describe('GestionJourneeComponent', () => {
  let component: GestionJourneeComponent;
  let fixture: ComponentFixture<GestionJourneeComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [GestionJourneeComponent]
    });
    fixture = TestBed.createComponent(GestionJourneeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
