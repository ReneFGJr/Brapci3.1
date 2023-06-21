import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BrapciHomeLivrosComponent } from './brapci-home-livros.component';

describe('BrapciHomeLivrosComponent', () => {
  let component: BrapciHomeLivrosComponent;
  let fixture: ComponentFixture<BrapciHomeLivrosComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [BrapciHomeLivrosComponent]
    });
    fixture = TestBed.createComponent(BrapciHomeLivrosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
