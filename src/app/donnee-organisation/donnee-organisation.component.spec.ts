import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DonneeOrganisationComponent } from './donnee-organisation.component';

describe('DonneeOrganisationComponent', () => {
  let component: DonneeOrganisationComponent;
  let fixture: ComponentFixture<DonneeOrganisationComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DonneeOrganisationComponent]
    });
    fixture = TestBed.createComponent(DonneeOrganisationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
