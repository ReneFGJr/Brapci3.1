import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-banner-book',
  templateUrl: './banner-book.component.html',
})
export class BannerBookComponent {
  @Input() public section: Array<any> | any;
  @Input() public publisher: string = '';
  @Input() public cover: string = '';
  @Input() public caption: string = '';
  ngOnInit() {}
}
