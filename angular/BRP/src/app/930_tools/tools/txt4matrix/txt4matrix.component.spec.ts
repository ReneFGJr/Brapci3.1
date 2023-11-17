import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Txt4matrixComponent } from './txt4matrix.component';

describe('Txt4matrixComponent', () => {
  let component: Txt4matrixComponent;
  let fixture: ComponentFixture<Txt4matrixComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [Txt4matrixComponent]
    });
    fixture = TestBed.createComponent(Txt4matrixComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
