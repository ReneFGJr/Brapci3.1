import { Component, OnInit } from '@angular/core';
import { VitrineLivrosService } from 'src/app/100_brapci_livros/service/vitrine-livros.service';
import { NgForm } from '@angular/forms';
import { UserService } from '../../../../001_auth/service/user.service';

@Component({
  selector: 'app-book-add',
  templateUrl: './book-add.component.html',
  styleUrls: ['./book-add.component.css']
})
export class BookAddComponent {
  public message = '';
  isbn: string = '9786587885049';
  public listBook: Array<any> | any;
  public itemBook: Array<any> | any;

  constructor(
    private vitrineLivrosService: VitrineLivrosService,
    private userService: UserService
  ) { }

  public user: Array<any> | any;

  ngOnInit() {
    this.user = this.userService.getUser()
  }

  onSubmit(f: NgForm) {
    this.checkISBN(this.isbn);
  }

  public insertISBN(isbn: string): boolean {

    /***************************** ISERIR ISNB NA BASE */
    this.vitrineLivrosService.insertISBN(isbn).subscribe(
      res => {
        this.itemBook = res;
        if (this.itemBook.valid) {
          console.log("Valido");
        } else {
          let status = <number>this.itemBook.status;
          let msg = <string>this.itemBook.message;
          let item = <string>this.itemBook.item;
          if (status == 200)
            {
              this.message = `${this.listBook.message} ${this.listBook.isbn13}`;
            } else {
              /********* 201 */
              console.log(this.itemBook);
              msg += " - " + 'Veja o item ' + item;
              this.message = msg;
            }
        }
      }
    );
    return true;
  }

  /********************************************************* CheckISBN */
  public checkISBN(isbn: string): boolean {
    if (isbn != '') {
      this.vitrineLivrosService.validISBN(isbn).subscribe(
        res => {
          this.listBook = res;
          if (this.listBook.valid) {
            this.insertISBN(isbn);
          } else {
            this.message = `Erro no ISSN ${this.listBook.isbn13}`;
          }
        }
      );
      return true;
    } else {
      this.message = `Informe um número de ISSN`;
      return false
    }
  }

}
