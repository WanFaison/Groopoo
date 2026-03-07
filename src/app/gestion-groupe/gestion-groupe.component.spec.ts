import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GestionGroupeComponent } from './gestion-groupe.component';

describe('GestionGroupeComponent', () => {
  let component: GestionGroupeComponent;
  let fixture: ComponentFixture<GestionGroupeComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [GestionGroupeComponent]
    });
    fixture = TestBed.createComponent(GestionGroupeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
