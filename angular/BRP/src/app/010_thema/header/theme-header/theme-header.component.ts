import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-theme-header',
  templateUrl: './theme-header.component.html',
})
export class ThemeHeaderComponent {
  @Input() public header: Array<any> | any;
  @Input() public data: Array<any> | any;
  constructor() {}
  ngOnInit() {}
}
