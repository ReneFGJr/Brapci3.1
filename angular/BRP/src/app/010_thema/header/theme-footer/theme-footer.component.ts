import { environment } from './../../../../environments/environment.prod';
import { Component } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-theme-footer',
  templateUrl: './theme-footer.component.html',
  styleUrls: ['./theme-footer.component.scss'],
})
export class ThemeFooterComponent {
  public brapci_data: number = 2010;
  public section: string = '';
  public version: string = 'version';
  constructor(private cookieService: CookieService) {}

  ngOnInit() {
    this.version = environment.version_system;
    this.brapci_data = new Date().getFullYear();
    console.log(this.brapci_data); // output 2020
    this.section = this.cookieService.get('section');
  }
}
