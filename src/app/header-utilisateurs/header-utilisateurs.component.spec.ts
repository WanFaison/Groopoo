import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HeaderUtilisateursComponent } from './header-utilisateurs.component';

describe('HeaderUtilisateursComponent', () => {
  let component: HeaderUtilisateursComponent;
  let fixture: ComponentFixture<HeaderUtilisateursComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [HeaderUtilisateursComponent]
    });
    fixture = TestBed.createComponent(HeaderUtilisateursComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
