import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FinalistComponent } from './finalist.component';

describe('FinalistComponent', () => {
  let component: FinalistComponent;
  let fixture: ComponentFixture<FinalistComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FinalistComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FinalistComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
