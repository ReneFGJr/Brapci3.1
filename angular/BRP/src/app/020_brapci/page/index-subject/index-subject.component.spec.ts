import { ComponentFixture, TestBed } from '@angular/core/testing';

import { IndexSubjectComponent } from './index-subject.component';

describe('IndexSubjectComponent', () => {
  let component: IndexSubjectComponent;
  let fixture: ComponentFixture<IndexSubjectComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [IndexSubjectComponent]
    });
    fixture = TestBed.createComponent(IndexSubjectComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
