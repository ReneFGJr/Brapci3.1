import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-banner-ebbc',
  templateUrl: './banner-ebbc.component.html',
  styleUrls: ['./banner-ebbc.component.scss'],
})
export class BannerEbbcComponent {
  @Input() public section: Array<any> | any;
  @Input() public publisher: string = '';
  @Input() public cover: string = '';
  @Input() public caption: string = '';

  logo_ebbc = '/assets/img/logo_ebbc.png';
  logo_ebbc_icone = '/assets/img/logo_ebbc.gif';
  ngOnInit() {}
}
