import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VitrineSearchComponent } from './vitrine-search.component';

describe('VitrineSearchComponent', () => {
  let component: VitrineSearchComponent;
  let fixture: ComponentFixture<VitrineSearchComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [VitrineSearchComponent]
    });
    fixture = TestBed.createComponent(VitrineSearchComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
