import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VComponent } from './v.component';

describe('VComponent', () => {
  let component: VComponent;
  let fixture: ComponentFixture<VComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VComponent]
    });
    fixture = TestBed.createComponent(VComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
