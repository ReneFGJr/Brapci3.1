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

  ngOnInit(): void {
    this.header = [];
    this.header = { title: 'Livro' };
  }
}
