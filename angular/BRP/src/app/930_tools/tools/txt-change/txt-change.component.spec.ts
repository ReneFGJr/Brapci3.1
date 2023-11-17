import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TxtChangeComponent } from './txt-change.component';

describe('TxtChangeComponent', () => {
  let component: TxtChangeComponent;
  let fixture: ComponentFixture<TxtChangeComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [TxtChangeComponent]
    });
    fixture = TestBed.createComponent(TxtChangeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
