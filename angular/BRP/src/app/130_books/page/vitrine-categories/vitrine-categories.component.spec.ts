import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VitrineCategoriesComponent } from './vitrine-categories.component';

describe('VitrineCategoriesComponent', () => {
  let component: VitrineCategoriesComponent;
  let fixture: ComponentFixture<VitrineCategoriesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VitrineCategoriesComponent]
    });
    fixture = TestBed.createComponent(VitrineCategoriesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
