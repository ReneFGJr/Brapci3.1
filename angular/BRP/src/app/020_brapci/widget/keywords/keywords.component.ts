import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-work-keywords',
  templateUrl: './keywords.component.html',
  styleUrls: ['./keywords.component.scss']
})
export class KeywordsComponent {
  @Input() public keywords:Array<any>|any
}
