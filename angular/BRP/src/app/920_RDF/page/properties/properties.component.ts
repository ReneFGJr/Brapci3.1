import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-rdf-properties',
  templateUrl: './properties.component.html',
  styleUrls: ['./properties.component.scss'],
})
export class RDFPropertiesComponent {
  @Input() public rdf: Array<any> | any;
}
