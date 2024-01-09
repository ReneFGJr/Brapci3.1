import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-search-tips',
  templateUrl: './tips.component.html',
  styleUrls: ['./tips.component.scss'],
})
export class TipsComponent {
  @Input() public terms: Array<any> | any;
}
