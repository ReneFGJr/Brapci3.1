import { BrapciService } from './../../010_services/brapci.service';
import { CookieService } from 'ngx-cookie-service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-main',
  templateUrl: 'main.component.html',
})
export class MainComponent {
  public header = { title: 'Brapci - Base de Dados em Ciência da Informação' }

  constructor(
    private cookieService:CookieService,
    private brapciService:BrapciService
  ) {}
  ngOnInit()
    {
      console.log('-------------INIT----');
      let url = 'brapci/setCookie'
      console.log(this)
      this.brapciService.api_post(url).subscribe((res) => {
        console.log(res)
      });
    }

}
