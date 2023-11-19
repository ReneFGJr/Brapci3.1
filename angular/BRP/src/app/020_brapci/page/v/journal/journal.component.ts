import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-journal',
  templateUrl: './journal.component.html',
})
export class BrapciJournalComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public rdf: string = '/assets/img/icone_rdf.png';
  public header: Array<any> | any = null;
}
