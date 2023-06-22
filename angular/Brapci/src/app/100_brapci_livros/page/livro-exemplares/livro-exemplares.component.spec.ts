import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LivroExemplaresComponent } from './livro-exemplares.component';

describe('LivroExemplaresComponent', () => {
  let component: LivroExemplaresComponent;
  let fixture: ComponentFixture<LivroExemplaresComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [LivroExemplaresComponent]
    });
    fixture = TestBed.createComponent(LivroExemplaresComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
