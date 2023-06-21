import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BookServicesComponent } from './book-services.component';

describe('BookServicesComponent', () => {
  let component: BookServicesComponent;
  let fixture: ComponentFixture<BookServicesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BookServicesComponent]
    });
    fixture = TestBed.createComponent(BookServicesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
