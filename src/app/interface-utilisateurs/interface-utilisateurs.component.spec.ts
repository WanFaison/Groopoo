import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InterfaceUtilisateursComponent } from './interface-utilisateurs.component';

describe('InterfaceUtilisateursComponent', () => {
  let component: InterfaceUtilisateursComponent;
  let fixture: ComponentFixture<InterfaceUtilisateursComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [InterfaceUtilisateursComponent]
    });
    fixture = TestBed.createComponent(InterfaceUtilisateursComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
