import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WelcomeSourceComponent } from './welcome-source.component';

describe('WelcomeSourceComponent', () => {
  let component: WelcomeSourceComponent;
  let fixture: ComponentFixture<WelcomeSourceComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [WelcomeSourceComponent]
    });
    fixture = TestBed.createComponent(WelcomeSourceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
