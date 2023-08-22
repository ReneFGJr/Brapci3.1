import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OaiButtonsComponent } from './oai-buttons.component';

describe('OaiButtonsComponent', () => {
  let component: OaiButtonsComponent;
  let fixture: ComponentFixture<OaiButtonsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [OaiButtonsComponent]
    });
    fixture = TestBed.createComponent(OaiButtonsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
