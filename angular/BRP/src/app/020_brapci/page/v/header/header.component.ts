import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent {
  @Input() public data:Array<any> | any
  @Input() public title:string = '';
}
