import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MainOAIComponent } from './main-oai.component';

describe('MainOAIComponent', () => {
  let component: MainOAIComponent;
  let fixture: ComponentFixture<MainOAIComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [MainOAIComponent]
    });
    fixture = TestBed.createComponent(MainOAIComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
