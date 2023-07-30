import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-journal-banner',
  templateUrl: './journal-banner.component.html',
  styleUrls: ['./journal-banner.component.scss']
})
export class JournalBannerComponent {
  @Input() public data:Array<any>|any

}
