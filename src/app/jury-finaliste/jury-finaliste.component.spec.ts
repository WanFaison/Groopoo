import { ComponentFixture, TestBed } from '@angular/core/testing';

import { JuryFinalisteComponent } from './jury-finaliste.component';

describe('JuryFinalisteComponent', () => {
  let component: JuryFinalisteComponent;
  let fixture: ComponentFixture<JuryFinalisteComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [JuryFinalisteComponent]
    });
    fixture = TestBed.createComponent(JuryFinalisteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
