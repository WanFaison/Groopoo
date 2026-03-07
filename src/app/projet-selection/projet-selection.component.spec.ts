import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProjetSelectionComponent } from './projet-selection.component';

describe('ProjetSelectionComponent', () => {
  let component: ProjetSelectionComponent;
  let fixture: ComponentFixture<ProjetSelectionComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ProjetSelectionComponent]
    });
    fixture = TestBed.createComponent(ProjetSelectionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
