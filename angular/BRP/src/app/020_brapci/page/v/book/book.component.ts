import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-book',
  templateUrl: './book.component.html'
})
export class BookComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;
  public section=[{name:"LIVROS"}]

  ngOnInit(): void {
    this.header = [];
    this.header = { title: 'Livro' };
  }
}
