import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InterfaceUtilisateursBloquerComponent } from './interface-utilisateurs-bloquer.component';

describe('InterfaceUtilisateursBloquerComponent', () => {
  let component: InterfaceUtilisateursBloquerComponent;
  let fixture: ComponentFixture<InterfaceUtilisateursBloquerComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [InterfaceUtilisateursBloquerComponent]
    });
    fixture = TestBed.createComponent(InterfaceUtilisateursBloquerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
