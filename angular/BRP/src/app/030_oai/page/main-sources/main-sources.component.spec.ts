import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MainSourcesComponent } from './main-sources.component';

describe('MainSourcesComponent', () => {
  let component: MainSourcesComponent;
  let fixture: ComponentFixture<MainSourcesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [MainSourcesComponent]
    });
    fixture = TestBed.createComponent(MainSourcesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
