import { BrapciService } from './../../010_services/brapci.service';
import { CookieService } from 'ngx-cookie-service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-main',
  templateUrl: 'main.component.html',
})
export class MainComponent {
  public header = { title: 'Base de Dados em Ciência da Informação' };
  public cookie: Array<any> | any = [];
  public data: Array<any> | any = [
    {
      description:
        'A Brapci, sigla para Base de Dados em Ciência da Informação, é uma plataforma digital brasileira dedicada à coleta, preservação e ao acesso de literatura científica na área de Ciência da Informação. Ela abrange uma ampla gama de publicações, incluindo artigos de periódicos, trabalhos de eventos, livros e capítulos de livros, principalmente de fontes brasileiras e américa latina.',
      keywords: 'Brapci; Arquivologia; Biblioteconomia; Ciência da Informação; Base de Dados'
    },
  ];
  public status_ok: number = 0;
  public status_message: string = 'Carragando...';

  constructor(public brapciService: BrapciService) {}

  ngOnInit() {
    console.log('Starting Services');
    this.brapciService.api_post('status').subscribe(
      (res) => {
        this.status_ok = 1;
      },
      (error) => {
        console.log(error);
        this.status_ok = 9;
      }
    );
  }
}
