import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-basket-select',
  templateUrl: './basket-select.component.html'
})
export class BasketSelectComponent {
  @Input() public data:Array<any> | any
}
