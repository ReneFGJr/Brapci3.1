import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-book-preparo-view',
  templateUrl: './book-preparo-view.component.html',
  styleUrls: ['./book-preparo-view.component.css']
})

export class BookPreparoViewComponent {

  constructor(private router: Router) {}
  @Input() public contador:number =  0;
  @Input() public isbn: string = "";
  @Input() public itemBook: Array<any> | any;
  @Input() public admin: boolean = false;
}
