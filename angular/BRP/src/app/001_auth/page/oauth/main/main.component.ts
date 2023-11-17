import { Component } from '@angular/core';

@Component({
  selector: 'app-main',
  template: '<app-theme-navbar></app-theme-navbar><router-outlet></router-outlet><app-theme-footer></app-theme-footer>',
})
export class MainOauthComponent {}
