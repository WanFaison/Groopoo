import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DonneeAnneeComponent } from './donnee-annee.component';

describe('DonneeAnneeComponent', () => {
  let component: DonneeAnneeComponent;
  let fixture: ComponentFixture<DonneeAnneeComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DonneeAnneeComponent]
    });
    fixture = TestBed.createComponent(DonneeAnneeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
