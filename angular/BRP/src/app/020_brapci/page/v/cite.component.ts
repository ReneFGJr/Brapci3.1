import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-v-cite',
  templateUrl: './cite.component.html',
  styleUrls: ['./v.component.scss']
})
export class CiteComponent {
  @Input() public citacao:Array<any>|any
}
