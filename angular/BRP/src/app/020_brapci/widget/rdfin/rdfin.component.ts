import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-rdfin',
  templateUrl: './rdfin.component.html',
  styleUrls: ['./rdfin.component.scss'],
})
export class RDFinComponent {
  @Input() public data: Array<any> | any;
}
