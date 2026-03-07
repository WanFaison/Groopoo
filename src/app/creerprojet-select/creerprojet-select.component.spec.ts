import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreerprojetSelectComponent } from './creerprojet-select.component';

describe('CreerprojetSelectComponent', () => {
  let component: CreerprojetSelectComponent;
  let fixture: ComponentFixture<CreerprojetSelectComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [CreerprojetSelectComponent]
    });
    fixture = TestBed.createComponent(CreerprojetSelectComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
