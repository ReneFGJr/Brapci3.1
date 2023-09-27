import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-banner-proceeding',
  templateUrl: './banner-proceeding.component.html',
  styleUrls: ['./banner.component.scss'],
})
export class BannerProceedingComponent {
  @Input() public section: Array<any> | any;
  @Input() public publisher: string = '';
  @Input() public cover: string = '';
  ngOnInit() {}
}
