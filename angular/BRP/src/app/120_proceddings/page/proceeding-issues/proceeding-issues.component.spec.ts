import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProceedingIssuesComponent } from './proceeding-issues.component';

describe('ProceedingIssuesComponent', () => {
  let component: ProceedingIssuesComponent;
  let fixture: ComponentFixture<ProceedingIssuesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ProceedingIssuesComponent]
    });
    fixture = TestBed.createComponent(ProceedingIssuesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
