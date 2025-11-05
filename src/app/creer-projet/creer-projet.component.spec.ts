import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreerProjetComponent } from './creer-projet.component';

describe('CreerProjetComponent', () => {
  let component: CreerProjetComponent;
  let fixture: ComponentFixture<CreerProjetComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [CreerProjetComponent]
    });
    fixture = TestBed.createComponent(CreerProjetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
