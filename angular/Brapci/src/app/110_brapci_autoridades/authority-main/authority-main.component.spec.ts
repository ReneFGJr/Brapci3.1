import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AuthorityMainComponent } from './authority-main.component';

describe('AuthorityMainComponent', () => {
  let component: AuthorityMainComponent;
  let fixture: ComponentFixture<AuthorityMainComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [AuthorityMainComponent]
    });
    fixture = TestBed.createComponent(AuthorityMainComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
