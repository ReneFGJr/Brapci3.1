import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Txt4unitComponent } from './txt4unit.component';

describe('Txt4unitComponent', () => {
  let component: Txt4unitComponent;
  let fixture: ComponentFixture<Txt4unitComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [Txt4unitComponent]
    });
    fixture = TestBed.createComponent(Txt4unitComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
