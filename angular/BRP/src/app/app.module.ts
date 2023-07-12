import { FormsModule } from '@angular/forms';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';

import { Error404Component } from './000_header/error404/error404.component';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { DeniedComponent } from './000_header/denied/denied.component';

import { NavbarComponent } from './000_header/navbar/navbar.component';
import { HeaderComponent } from './000_header/header/header.component';
import { FooterComponent } from './000_header/footer/footer.component';
import { HomepageComponent } from './000_header/homepage/homepage.component';

@NgModule({
  declarations: [
    AppComponent,
    Error404Component,
    DeniedComponent,
    NavbarComponent,
    HeaderComponent,
    FooterComponent,
    HomepageComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    RouterModule,
    CommonModule,
    FormsModule,
    HttpClientModule
  ],
  providers: [],
  schemas: [
    CUSTOM_ELEMENTS_SCHEMA
  ],
  bootstrap: [AppComponent],
  exports:
    [
      NavbarComponent,
      HeaderComponent,
      FooterComponent,
    ]
})
export class AppModule { }
