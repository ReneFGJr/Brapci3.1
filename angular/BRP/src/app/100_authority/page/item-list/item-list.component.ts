import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-authority-item-list',
  templateUrl: './item-list.component.html',
  styleUrls: ['./item-list.component.scss']
})
export class ItemListAuthorityComponent {

  @Input() public lista: Array<any> | any;

}
