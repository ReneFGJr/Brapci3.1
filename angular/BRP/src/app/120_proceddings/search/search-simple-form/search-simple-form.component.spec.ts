import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SearchSimpleFormComponent } from './search-simple-form.component';

describe('SearchSimpleFormComponent', () => {
  let component: SearchSimpleFormComponent;
  let fixture: ComponentFixture<SearchSimpleFormComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [SearchSimpleFormComponent]
    });
    fixture = TestBed.createComponent(SearchSimpleFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
