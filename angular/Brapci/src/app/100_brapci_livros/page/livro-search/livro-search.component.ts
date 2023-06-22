import { Component, EventEmitter, Output } from '@angular/core';

@Component({
  selector: 'app-livro-search',
  templateUrl: './livro-search.component.html',
  styleUrls: ['./livro-search.component.css']
})
export class LivroSearchComponent {

  @Output() public emmitSearch: EventEmitter<string> = new EventEmitter();

  constructor() { }

public search(value: string)
  {
   this.emmitSearch.emit(value);
  }
}
