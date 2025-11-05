import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DonneeCoachsComponent } from './donnee-coachs.component';

describe('DonneeCoachsComponent', () => {
  let component: DonneeCoachsComponent;
  let fixture: ComponentFixture<DonneeCoachsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DonneeCoachsComponent]
    });
    fixture = TestBed.createComponent(DonneeCoachsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
