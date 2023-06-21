import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewAuthorityComponent } from './view-authority.component';

describe('ViewAuthorityComponent', () => {
  let component: ViewAuthorityComponent;
  let fixture: ComponentFixture<ViewAuthorityComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ViewAuthorityComponent]
    });
    fixture = TestBed.createComponent(ViewAuthorityComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
