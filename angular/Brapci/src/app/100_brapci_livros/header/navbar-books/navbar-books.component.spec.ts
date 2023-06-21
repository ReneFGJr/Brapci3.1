import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NavbarBooksComponent } from './navbar-books.component';

describe('NavbarBooksComponent', () => {
  let component: NavbarBooksComponent;
  let fixture: ComponentFixture<NavbarBooksComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [NavbarBooksComponent]
    });
    fixture = TestBed.createComponent(NavbarBooksComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
