import { Component } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {

  constructor(private router: Router) {}

  public urlAtual: string = '';
  public urlAnterior: string = '';
  public navbar = true;
  public navbar_book = false;

  ngOnInit() {
    this.router.events.subscribe((e: any) => {
      if (e instanceof NavigationEnd) {
        this.urlAnterior = this.urlAtual;
        this.urlAtual = e.url;
        console.log(this.urlAnterior);
        console.log(this.urlAtual);

        if (this.urlAtual == '/books')
          {
            this.navbar = false;
            this.navbar_book = true;
          }
      }
    });
  }

  title = 'Brapci';

}
