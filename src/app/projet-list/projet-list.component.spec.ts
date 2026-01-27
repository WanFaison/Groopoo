import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProjetListComponent } from './projet-list.component';

describe('ProjetListComponent', () => {
  let component: ProjetListComponent;
  let fixture: ComponentFixture<ProjetListComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ProjetListComponent]
    });
    fixture = TestBed.createComponent(ProjetListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
