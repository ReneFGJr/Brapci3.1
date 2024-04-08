import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EbbcComponent } from './ebbc.component';

describe('EbbcComponent', () => {
  let component: EbbcComponent;
  let fixture: ComponentFixture<EbbcComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [EbbcComponent]
    });
    fixture = TestBed.createComponent(EbbcComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
