import { ComponentFixture, TestBed } from '@angular/core/testing';

import { JounalsComponent } from './jounals.component';

describe('JounalsComponent', () => {
  let component: JounalsComponent;
  let fixture: ComponentFixture<JounalsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [JounalsComponent]
    });
    fixture = TestBed.createComponent(JounalsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
