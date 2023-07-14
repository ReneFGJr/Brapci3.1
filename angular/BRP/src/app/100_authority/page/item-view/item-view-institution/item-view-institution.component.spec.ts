import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemViewInstitutionComponent } from './item-view-institution.component';

describe('ItemViewInstitutionComponent', () => {
  let component: ItemViewInstitutionComponent;
  let fixture: ComponentFixture<ItemViewInstitutionComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ItemViewInstitutionComponent]
    });
    fixture = TestBed.createComponent(ItemViewInstitutionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
