import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-v-cite',
  templateUrl: './cite.component.html'
})
export class CiteComponent {
  @Input() public citacao:Array<any>|any
}
