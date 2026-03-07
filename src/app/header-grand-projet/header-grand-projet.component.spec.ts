import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HeaderGrandProjetComponent } from './header-grand-projet.component';

describe('HeaderGrandProjetComponent', () => {
  let component: HeaderGrandProjetComponent;
  let fixture: ComponentFixture<HeaderGrandProjetComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [HeaderGrandProjetComponent]
    });
    fixture = TestBed.createComponent(HeaderGrandProjetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
