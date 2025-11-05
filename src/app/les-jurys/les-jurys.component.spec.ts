import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LesJurysComponent } from './les-jurys.component';

describe('LesJurysComponent', () => {
  let component: LesJurysComponent;
  let fixture: ComponentFixture<LesJurysComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [LesJurysComponent]
    });
    fixture = TestBed.createComponent(LesJurysComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
