import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GroupeEmargementComponent } from './groupe-emargement.component';

describe('GroupeEmargementComponent', () => {
  let component: GroupeEmargementComponent;
  let fixture: ComponentFixture<GroupeEmargementComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [GroupeEmargementComponent]
    });
    fixture = TestBed.createComponent(GroupeEmargementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
