import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-theme-header',
  templateUrl: './theme-header.component.html'
})
export class ThemeHeaderComponent {
  @Input() public header:Array<any>|any
  constructor() { }
  ngOnInit() {
    console.log(this.header);
  }
}
