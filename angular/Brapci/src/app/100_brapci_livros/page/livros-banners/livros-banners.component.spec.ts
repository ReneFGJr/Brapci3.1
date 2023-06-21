import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LivrosBannersComponent } from './livros-banners.component';

describe('LivrosBannersComponent', () => {
  let component: LivrosBannersComponent;
  let fixture: ComponentFixture<LivrosBannersComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [LivrosBannersComponent]
    });
    fixture = TestBed.createComponent(LivrosBannersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
