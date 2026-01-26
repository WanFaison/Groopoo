import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormTheme } from './form-theme';

describe('FormTheme', () => {
  let component: FormTheme;
  let fixture: ComponentFixture<FormTheme>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormTheme]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FormTheme);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
