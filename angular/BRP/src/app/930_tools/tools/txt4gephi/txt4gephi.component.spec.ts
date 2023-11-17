import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Txt4gephiComponent } from './txt4gephi.component';

describe('Txt4gephiComponent', () => {
  let component: Txt4gephiComponent;
  let fixture: ComponentFixture<Txt4gephiComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [Txt4gephiComponent]
    });
    fixture = TestBed.createComponent(Txt4gephiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
