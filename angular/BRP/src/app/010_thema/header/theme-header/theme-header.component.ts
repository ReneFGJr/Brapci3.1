import { Component, Input, OnInit } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-theme-header',
  templateUrl: './theme-header.component.html',
})
export class ThemeHeaderComponent {
  @Input() public header: Array<any> | any;
  @Input() public data: Array<any> | any;
  public cookie: Array<any> | any = [];
  constructor(
    private cookieService: CookieService,
    private brapciService: BrapciService
  ) {}

  ngOnInit() {
    if (this.cookieService.check('section')) {
      this.cookie.cookie = this.cookieService.get('section');
      this.cookieService.set('section', this.cookie.cookie);
    } else {
      let url = 'brapci/setCookie';
      this.brapciService.api_post(url).subscribe((res) => {
        this.cookie = res;
        this.cookieService.set('section', this.cookie.cookie);
      });
    }
  }
}
