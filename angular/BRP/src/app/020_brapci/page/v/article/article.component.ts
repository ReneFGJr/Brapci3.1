import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-article',
  templateUrl: './article.component.html',
})
export class ArticleComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;
  public langs: Array<any> = ['pt', 'en', 'es', 'fr'];
  public abstract: Array<any> = [{'pt':'Resumo', 'en':'Abstract', 'es':'Resumen', 'fr':'Résumé'}];
  public keywords: Array<any> = [{'pt':'Palavras-chave', 'en':'Keywords', 'es':'Palabras clave', 'fr':'Mots clés'}];
  objectKeys = Object.keys;
  objectValues = Object.values;

  ngOnInit(): void {
    console.log(this.data);
    this.header = [];
    this.header = { title: 'Artigo' };
    //this.url = this.data.id;
    //console.log(this.data);
    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.
  }
}
