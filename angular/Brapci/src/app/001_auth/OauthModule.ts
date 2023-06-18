import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserNavBarComponent } from './page/user-nav-bar/user-nav-bar.component';
import { UserLoginComponent } from './page/user-login/user-login.component';
import { OauthComponent } from './oauth/oauth.component';
import { FormsModule } from '@angular/forms';
import { AppRoutingModule } from '../app-routing.module';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';


@NgModule({
  declarations: [
    UserNavBarComponent,
    UserLoginComponent,
    OauthComponent,
  ],
  imports: [
    CommonModule,
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    HttpClientModule
  ],
  exports: [
    UserNavBarComponent
  ],
  providers: [],
  bootstrap: []
})
export class OauthModule { }
