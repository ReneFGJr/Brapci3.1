import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-v-number',
  templateUrl: './number.component.html'
})
export class NumberComponent {
  @Input() public data:Array<any> | any
}
