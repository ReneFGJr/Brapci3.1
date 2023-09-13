import { Component } from '@angular/core';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  public header:Array<any>|any
  constructor()
    {
      this.header = {title:'Brapci Livros'};
    }
ngOnInit()
  {
    console.log("Hello BOOKS")
  }
}
