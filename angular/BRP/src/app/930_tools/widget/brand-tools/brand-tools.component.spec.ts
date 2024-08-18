import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BrandToolsComponent } from './brand-tools.component';

describe('BrandToolsComponent', () => {
  let component: BrandToolsComponent;
  let fixture: ComponentFixture<BrandToolsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BrandToolsComponent]
    });
    fixture = TestBed.createComponent(BrandToolsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
