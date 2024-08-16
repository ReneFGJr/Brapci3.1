import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BannerBrapciLivrosComponent } from './banner-brapci-livros.component';

describe('BannerBrapciLivrosComponent', () => {
  let component: BannerBrapciLivrosComponent;
  let fixture: ComponentFixture<BannerBrapciLivrosComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BannerBrapciLivrosComponent]
    });
    fixture = TestBed.createComponent(BannerBrapciLivrosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
