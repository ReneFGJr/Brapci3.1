import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewSourceComponent } from './view-source.component';

describe('ViewSourceComponent', () => {
  let component: ViewSourceComponent;
  let fixture: ComponentFixture<ViewSourceComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ViewSourceComponent]
    });
    fixture = TestBed.createComponent(ViewSourceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
