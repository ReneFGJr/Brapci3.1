import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-rdf-classes',
  templateUrl: './classes.component.html',
  styleUrls: ['./classes.component.scss']
})
export class RDFClassesComponent {
  @Input() public rdf:Array<any>|any

}
