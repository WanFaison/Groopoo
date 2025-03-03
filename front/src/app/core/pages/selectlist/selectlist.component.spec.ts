import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SelectlistComponent } from './selectlist.component';

describe('SelectlistComponent', () => {
  let component: SelectlistComponent;
  let fixture: ComponentFixture<SelectlistComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SelectlistComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SelectlistComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
