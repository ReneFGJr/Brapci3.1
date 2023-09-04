import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-article',
  templateUrl: './article.component.html',
  styleUrls: ['./article.component.scss'],
})
export class ArticleComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header:Array<any>|any = null

  ngOnInit(): void {
    this.header = []
    this.header = {'title':'Artigo'}
    //this.url = this.data.id;
    //console.log(this.data);
    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.
  }
}
