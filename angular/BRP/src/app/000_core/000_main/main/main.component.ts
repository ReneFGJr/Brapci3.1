import { BrapciService } from './../../010_services/brapci.service';
import { CookieService } from 'ngx-cookie-service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-main',
  templateUrl: 'main.component.html',
})
export class MainComponent {
  public header = { title: 'Brapci - Base de Dados em Ciência da Informação' };
  public cookie: Array<any> | any = [];
  public status_ok: number = 0;
  public status_message: string = 'Carragando...';

  constructor(public brapciService: BrapciService) {}

  ngOnInit() {
    console.log('Starting Services');
      this.brapciService.api_post('status').subscribe(
        (res) => {},
        (error) => {
          this.status_ok = 9
        }
      )
  }
}
