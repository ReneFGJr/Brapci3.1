import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TimelineJournalComponent } from './timeline-journal.component';

describe('TimelineJournalComponent', () => {
  let component: TimelineJournalComponent;
  let fixture: ComponentFixture<TimelineJournalComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [TimelineJournalComponent]
    });
    fixture = TestBed.createComponent(TimelineJournalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
