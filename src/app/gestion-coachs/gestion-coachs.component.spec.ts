import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GestionCoachsComponent } from './gestion-coachs.component';

describe('GestionCoachsComponent', () => {
  let component: GestionCoachsComponent;
  let fixture: ComponentFixture<GestionCoachsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [GestionCoachsComponent]
    });
    fixture = TestBed.createComponent(GestionCoachsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
