import { Component } from '@angular/core';
import { VitrineLivrosService } from 'src/app/100_brapci_livros/service/vitrine-livros.service';
import { NgForm } from '@angular/forms';

@Component({
  selector: 'app-book-add',
  templateUrl: './book-add.component.html',
  styleUrls: ['./book-add.component.css']
})
export class BookAddComponent {
  public message='';

  constructor(
    private vitrineLivrosService: VitrineLivrosService
  ) {}

  isbn: string = '';

  onSubmit(f: NgForm) {
    this.message = this.checkISBN(f.value.isbn);
  }

  public checkISBN(f: NgForm):string
    {
      alert(this.isbn);
      //alert(f.isbn);
      return "OK";
    }

}
