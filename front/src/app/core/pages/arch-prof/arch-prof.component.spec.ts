import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ArchProfComponent } from './arch-prof.component';

describe('ArchProfComponent', () => {
  let component: ArchProfComponent;
  let fixture: ComponentFixture<ArchProfComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ArchProfComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ArchProfComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
