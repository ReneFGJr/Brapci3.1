import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HeaderComponent } from './000_header/header/header.component';
import { FooterComponent } from './000_header/footer/footer.component';
import { NavbarComponent } from './000_header/navbar/navbar.component';
import { OauthComponent } from './001_auth/oauth/oauth.component';
import { HomeComponent } from './002_main/home/home.component';
import { AboutComponent } from './002_main/home/about/about.component';
import { MainComponent } from './002_main/home/main/main.component';

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    FooterComponent,
    NavbarComponent,
    OauthComponent,
    HomeComponent,
    AboutComponent,
    MainComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
