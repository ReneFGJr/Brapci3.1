import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LivroVitrineComponent } from './livro-vitrine.component';

describe('LivroVitrineComponent', () => {
  let component: LivroVitrineComponent;
  let fixture: ComponentFixture<LivroVitrineComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [LivroVitrineComponent]
    });
    fixture = TestBed.createComponent(LivroVitrineComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
