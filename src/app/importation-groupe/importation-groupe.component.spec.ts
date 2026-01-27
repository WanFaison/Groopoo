import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ImportationGroupeComponent } from './importation-groupe.component';

describe('ImportationGroupeComponent', () => {
  let component: ImportationGroupeComponent;
  let fixture: ComponentFixture<ImportationGroupeComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ImportationGroupeComponent]
    });
    fixture = TestBed.createComponent(ImportationGroupeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
