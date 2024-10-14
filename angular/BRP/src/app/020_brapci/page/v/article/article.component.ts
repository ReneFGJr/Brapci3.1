import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-article',
  templateUrl: './article.component.html',
})
export class ArticleComponent {
  @Input() public data: Array<any> | any;
  public userID: Array<any> | any = null;

  constructor(
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService
  ) {}

  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;

  public fulltext: Array<any> | any = null;
  public langs: Array<any> = ['pt', 'en', 'es', 'fr'];
  public abstract: Array<any> = [
    { pt: 'Resumo', en: 'Abstract', es: 'Resumen', fr: 'Résumé' },
  ];
  public keywords: Array<any> = [
    {
      pt: 'Palavras-chave',
      en: 'Keywords',
      es: 'Palabras clave',
      fr: 'Mots clés',
    },
  ];
  objectKeys = Object.keys;
  objectValues = Object.values;

  ngOnInit(): void {
    this.userID = this.localStorageService.get('user');

    this.header = [];
    this.header = { title: 'Artigo' };
  }

  ViewFull() {
    let data = [{ id: this.data.ID }];
    this.brapciService.api_post('fulltext').subscribe((res) => {
      this.fulltext = res;
    });

    alert('VIEW' + this.data.ID);
  }
}
