import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-person',
  templateUrl: './person.component.html',
  styleUrls: ['./person.component.scss'],
})
export class PersonComponent {
  @Input() public data: Array<any> | any;
}
