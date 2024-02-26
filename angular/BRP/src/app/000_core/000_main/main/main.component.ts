import { BrapciService } from './../../010_services/brapci.service';
import { CookieService } from 'ngx-cookie-service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-main',
  templateUrl: 'main.component.html',
})
export class MainComponent {
  public header = { title: 'Brapci - Base de Dados em Ciência da Informação' }
  public cookie:Array<any> | any = []

  constructor() {}
  ngOnInit()
    {

    }

}
