import { Component } from '@angular/core';

@Component({
  selector: 'app-books-vitrine',
  templateUrl: './vitrine.component.html',
  styleUrls: ['./vitrine.component.scss'],
})
export class BooksVitrineComponent {
  public title: string = 'Brapci Books';
  public banner1: string = '/assets/banners/brapci_livros_01.png';
}
