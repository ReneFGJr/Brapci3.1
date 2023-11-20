import { Component, Input } from '@angular/core';
import { BrapciService } from '../../../000_core/010_services/brapci.service';

@Component({
  selector: 'app-brapci-banner',
  templateUrl: './banner.component.html',
})
export class BannerComponent {
  public results: Array<any> | any;
  @Input() public section: Array<any> | any;
  @Input() public cover: Array<any> | any;
  @Input() public publisher: Array<any> | any;

  constructor(private brapciService: BrapciService) {}
}
