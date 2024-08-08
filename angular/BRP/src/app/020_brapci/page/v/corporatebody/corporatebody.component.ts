import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-corporatebody',
  templateUrl: './corporatebody.component.html'
})
export class CorporatebodyComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;
  public section = [{ name: 'CorporateBody' }];

  ngOnInit(): void {
    this.header = [];
    this.header = { title: 'Institução' };
  }
}
