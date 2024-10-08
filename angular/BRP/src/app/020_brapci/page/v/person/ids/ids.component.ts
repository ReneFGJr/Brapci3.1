import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-person-ids',
  templateUrl: './ids.component.html',
  styleUrls: ['./ids.component.scss'],
})
export class IdsComponent {
  @Input() public icones:Array<any> | any
}
