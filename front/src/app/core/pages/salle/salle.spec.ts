import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Salle } from './salle';

describe('Salle', () => {
  let component: Salle;
  let fixture: ComponentFixture<Salle>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Salle]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Salle);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
