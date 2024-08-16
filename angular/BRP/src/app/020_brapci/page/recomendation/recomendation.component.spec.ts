import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RecomendationComponent } from './recomendation.component';

describe('RecomendationComponent', () => {
  let component: RecomendationComponent;
  let fixture: ComponentFixture<RecomendationComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [RecomendationComponent]
    });
    fixture = TestBed.createComponent(RecomendationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
