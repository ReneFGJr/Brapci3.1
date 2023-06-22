import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LivroSumarioComponent } from './livro-sumario.component';

describe('LivroSumarioComponent', () => {
  let component: LivroSumarioComponent;
  let fixture: ComponentFixture<LivroSumarioComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [LivroSumarioComponent]
    });
    fixture = TestBed.createComponent(LivroSumarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
