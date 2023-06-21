import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LivrosMainComponent } from './livros-main.component';

describe('LivrosMainComponent', () => {
  let component: LivrosMainComponent;
  let fixture: ComponentFixture<LivrosMainComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [LivrosMainComponent]
    });
    fixture = TestBed.createComponent(LivrosMainComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
