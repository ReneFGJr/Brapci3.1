import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-benancib',
  templateUrl: './benancib.component.html',
  styleUrls: ['./benancib.component.scss'],
})
export class BenancibComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;
  public section = [{ name: 'LIVRO' }];
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
    this.header = [];
    this.header = { title: 'Livro' };
  }
}
