import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SourceEditComponent } from './source-edit.component';

describe('SourceEditComponent', () => {
  let component: SourceEditComponent;
  let fixture: ComponentFixture<SourceEditComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [SourceEditComponent]
    });
    fixture = TestBed.createComponent(SourceEditComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
