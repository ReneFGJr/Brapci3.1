import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BenancibComponent } from './benancib.component';

describe('BenancibComponent', () => {
  let component: BenancibComponent;
  let fixture: ComponentFixture<BenancibComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BenancibComponent]
    });
    fixture = TestBed.createComponent(BenancibComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
