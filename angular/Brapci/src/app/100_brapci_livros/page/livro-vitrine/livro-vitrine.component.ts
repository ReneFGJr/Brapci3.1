import { Component, OnInit } from '@angular/core';
import { UIbooks } from '../../interface/UIbooks';
import { VitrineLivrosService } from '../../service/vitrine-livros.service';
@Component({
  selector: 'app-livro-vitrine',
  templateUrl: './livro-vitrine.component.html',
  styleUrls: ['./livro-vitrine.component.css']
})
export class LivroVitrineComponent {
  public listBook:UIbooks | any;
  private _listBook: UIbooks | any;

  constructor(
    private vitrineLivrosService: VitrineLivrosService,
  ) {}

  ngOnInit() {
        this.vitrineLivrosService.listBooks().subscribe(
          res => {
            this.listBook = res,
            this._listBook = res
          }
        );
  }

  public getSearch(value: string) {
    const filter = this._listBook.filter(( res:any ) =>
      {
        return !res.be_full.indexOf(value.toLowerCase())
    });
    this.listBook = filter;
  }
}
