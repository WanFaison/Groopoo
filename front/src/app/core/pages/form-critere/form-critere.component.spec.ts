import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormCritereComponent } from './form-critere.component';

describe('FormCritereComponent', () => {
  let component: FormCritereComponent;
  let fixture: ComponentFixture<FormCritereComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormCritereComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FormCritereComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
