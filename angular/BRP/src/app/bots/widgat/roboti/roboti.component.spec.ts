import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RobotiComponent } from './roboti.component';

describe('RobotiComponent', () => {
  let component: RobotiComponent;
  let fixture: ComponentFixture<RobotiComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [RobotiComponent]
    });
    fixture = TestBed.createComponent(RobotiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
