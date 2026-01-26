import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Etage } from './etage';

describe('Etage', () => {
  let component: Etage;
  let fixture: ComponentFixture<Etage>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Etage]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Etage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
