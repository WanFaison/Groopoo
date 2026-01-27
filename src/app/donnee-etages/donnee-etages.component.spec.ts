import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DonneeEtagesComponent } from './donnee-etages.component';

describe('DonneeEtagesComponent', () => {
  let component: DonneeEtagesComponent;
  let fixture: ComponentFixture<DonneeEtagesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DonneeEtagesComponent]
    });
    fixture = TestBed.createComponent(DonneeEtagesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
