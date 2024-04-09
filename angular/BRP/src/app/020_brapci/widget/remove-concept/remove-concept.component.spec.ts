import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RemoveConceptComponent } from './remove-concept.component';

describe('RemoveConceptComponent', () => {
  let component: RemoveConceptComponent;
  let fixture: ComponentFixture<RemoveConceptComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [RemoveConceptComponent]
    });
    fixture = TestBed.createComponent(RemoveConceptComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
