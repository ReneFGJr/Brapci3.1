import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-tabs-nav',
  templateUrl: './tabs-nav.component.html',
})
export class TabsNavComponent {
  @Input() public data: Array<any> | any;

}
