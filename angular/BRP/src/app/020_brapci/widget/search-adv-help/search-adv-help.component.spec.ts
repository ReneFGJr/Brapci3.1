import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SearchAdvHelpComponent } from './search-adv-help.component';

describe('SearchAdvHelpComponent', () => {
  let component: SearchAdvHelpComponent;
  let fixture: ComponentFixture<SearchAdvHelpComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [SearchAdvHelpComponent]
    });
    fixture = TestBed.createComponent(SearchAdvHelpComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
