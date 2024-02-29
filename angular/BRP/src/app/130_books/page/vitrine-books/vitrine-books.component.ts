import { BrapciService } from './../../../000_core/010_services/brapci.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-vitrine-books',
  templateUrl: './vitrine-books.component.html'
})
export class VitrineBooksComponent {

  public books:Array<any>|any

  constructor(
    private brapciService: BrapciService
  ) {}

  ngOnInit()
    {
    this.brapciService.api_post('book/vitrine').subscribe((res) => {
        console.log(res);
        this.books = res;
      });
    }

}
