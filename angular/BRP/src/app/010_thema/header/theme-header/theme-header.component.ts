import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-theme-header',
  templateUrl: './theme-header.component.html',
  styleUrls: ['./theme-header.component.scss']
})
export class ThemeHeaderComponent {
  @Input() public header:Array<any>|any
  constructor() { }
  ngOnInit() {
  }
}
