import { FormsModule } from '@angular/forms';
import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';

import { HeaderComponent } from './000_header/header/header.component';
import { Error404Component } from './000_header/error404/error404.component';
import { FooterComponent } from './000_header/footer/footer.component';
import { NavbarComponent } from './000_header/navbar/navbar.component';
import { HomeComponent } from './010_main/page/home/home.component';
import { WelcomeComponent } from './000_header/welcome/welcome.component';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { DeniedComponent } from './000_header/denied/denied.component';



@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    Error404Component,
    FooterComponent,
    NavbarComponent,
    HomeComponent,
    WelcomeComponent,
    DeniedComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    RouterModule,
    CommonModule,
    FormsModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
