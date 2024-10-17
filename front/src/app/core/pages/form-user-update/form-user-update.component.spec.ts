import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormUserUpdateComponent } from './form-user-update.component';

describe('FormUserUpdateComponent', () => {
  let component: FormUserUpdateComponent;
  let fixture: ComponentFixture<FormUserUpdateComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormUserUpdateComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FormUserUpdateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
