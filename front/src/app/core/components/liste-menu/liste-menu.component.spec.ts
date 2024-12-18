import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListeMenuComponent } from './liste-menu.component';

describe('ListeMenuComponent', () => {
  let component: ListeMenuComponent;
  let fixture: ComponentFixture<ListeMenuComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ListeMenuComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListeMenuComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
