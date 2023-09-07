import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-book-term',
  templateUrl: './term.component.html',
  styleUrls: ['./term.component.scss'],
})
export class BookTermComponent {
  @Input() public title: string = '';
  @Input() public autor: string = '';
  @Input() public licence: string = 'CC-BY-NC';

  public repositorio: string = 'Base de dados da BrapciLivros';
}
