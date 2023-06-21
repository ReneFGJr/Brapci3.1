import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { OauthComponent } from '../001_auth/oauth/oauth.component';
import { FormsModule } from '@angular/forms';
import { AppRoutingModule } from '../app-routing.module';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';

@NgModule({
  declarations: [
  ],
  imports: [
    CommonModule,
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    HttpClientModule
  ],
  exports: [
  ],
  providers: [],
  bootstrap: []
})
export class FindModule { }
