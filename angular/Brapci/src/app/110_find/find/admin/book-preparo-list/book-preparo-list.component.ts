import { Component } from '@angular/core';
import { VitrineLivrosService } from 'src/app/100_brapci_livros/service/vitrine-livros.service';

@Component({
  selector: 'app-book-preparo-list',
  templateUrl: './book-preparo-list.component.html',
  styleUrls: ['./book-preparo-list.component.css']
})

export class BookPreparoListComponent {

  public PreparoItems: Array<any> | any;

  constructor(
    private vitrineLivrosService: VitrineLivrosService
  ) { }

  show($isbn:string)
    {
      alert("OK");
    }

  xngOnInit()
    {

    }

}
