import { ComponentFixture, TestBed } from '@angular/core/testing';

import { JournalViewComponent } from './journal-view.component';

describe('JournalViewComponent', () => {
  let component: JournalViewComponent;
  let fixture: ComponentFixture<JournalViewComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [JournalViewComponent]
    });
    fixture = TestBed.createComponent(JournalViewComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
