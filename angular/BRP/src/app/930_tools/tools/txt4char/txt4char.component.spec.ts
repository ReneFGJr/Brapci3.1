import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Txt4charComponent } from './txt4char.component';

describe('Txt4charComponent', () => {
  let component: Txt4charComponent;
  let fixture: ComponentFixture<Txt4charComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [Txt4charComponent]
    });
    fixture = TestBed.createComponent(Txt4charComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
