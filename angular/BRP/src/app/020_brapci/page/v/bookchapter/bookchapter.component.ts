import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-bookchapter',
  templateUrl: './bookchapter.component.html',
})
export class BookchapterComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;
  public section = [{ name: 'CAPITULO' }];
}
