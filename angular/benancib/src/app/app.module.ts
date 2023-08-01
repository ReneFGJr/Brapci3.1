import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HeadComponent } from './page/head/head/head.component';
import { FootComponent } from './page/head/foot/foot.component';
import { NavbarComponent } from './page/head/navbar/navbar.component';
import { MainComponent } from './page/main/main.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ViewComponent } from './page/view/view.component';
import { SearchComponent } from './page/search/search.component';
import { AboutComponent } from './page/about/about.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

@NgModule({
  declarations: [
    AppComponent,
    HeadComponent,
    FootComponent,
    NavbarComponent,
    MainComponent,
    ViewComponent,
    SearchComponent,
    AboutComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule,
    ReactiveFormsModule,
    RouterModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
