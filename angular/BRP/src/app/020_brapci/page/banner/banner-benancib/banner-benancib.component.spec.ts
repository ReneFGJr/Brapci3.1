import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BannerBenancibComponent } from './banner-benancib.component';

describe('BannerBenancibComponent', () => {
  let component: BannerBenancibComponent;
  let fixture: ComponentFixture<BannerBenancibComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BannerBenancibComponent]
    });
    fixture = TestBed.createComponent(BannerBenancibComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
