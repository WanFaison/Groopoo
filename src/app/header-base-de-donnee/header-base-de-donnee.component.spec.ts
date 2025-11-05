import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HeaderBaseDeDonneeComponent } from './header-base-de-donnee.component';

describe('HeaderBaseDeDonneeComponent', () => {
  let component: HeaderBaseDeDonneeComponent;
  let fixture: ComponentFixture<HeaderBaseDeDonneeComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [HeaderBaseDeDonneeComponent]
    });
    fixture = TestBed.createComponent(HeaderBaseDeDonneeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
