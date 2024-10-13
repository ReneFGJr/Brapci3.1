import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProcessingWorkflowComponent } from './processing-workflow.component';

describe('ProcessingWorkflowComponent', () => {
  let component: ProcessingWorkflowComponent;
  let fixture: ComponentFixture<ProcessingWorkflowComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ProcessingWorkflowComponent]
    });
    fixture = TestBed.createComponent(ProcessingWorkflowComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
