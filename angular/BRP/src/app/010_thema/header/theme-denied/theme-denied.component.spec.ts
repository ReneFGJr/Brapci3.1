import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ThemeDeniedComponent } from './theme-denied.component';

describe('ThemeDeniedComponent', () => {
  let component: ThemeDeniedComponent;
  let fixture: ComponentFixture<ThemeDeniedComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ThemeDeniedComponent]
    });
    fixture = TestBed.createComponent(ThemeDeniedComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
