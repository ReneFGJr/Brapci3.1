import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-banner-general',
  templateUrl: './banner.component.html',
  styleUrls: ['./banner.component.scss'],
})
export class BrapciBannerComponent {
  @Input() public section: string = '';
  @Input() public publisher: string = '';
  @Input() public cover: string = '';
}
