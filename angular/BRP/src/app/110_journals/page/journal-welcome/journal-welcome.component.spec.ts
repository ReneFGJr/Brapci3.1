import { ComponentFixture, TestBed } from '@angular/core/testing';

import { JournalWelcomeComponent } from './journal-welcome.component';

describe('JournalWelcomeComponent', () => {
  let component: JournalWelcomeComponent;
  let fixture: ComponentFixture<JournalWelcomeComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [JournalWelcomeComponent]
    });
    fixture = TestBed.createComponent(JournalWelcomeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
