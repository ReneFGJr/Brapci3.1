import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-banner-benancib',
  templateUrl: './banner-benancib.component.html',
  styleUrls: ['./banner-benancib.component.scss'],
})
export class BannerBenancibComponent {
  @Input() public section: Array<any> | any;
  @Input() public publisher: string = '';
  @Input() public cover: string = '';
  @Input() public caption: string = '';

  logo_benancib = '/assets/img/logo_benancib.png';
  logo_benancib_icone = '/assets/img/logo_benancib.gif';
  ngOnInit() {}
}
