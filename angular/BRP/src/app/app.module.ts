import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

/* Modules */
import { ThemeModule } from './010_thema/theme.module';
import { HttpClientModule } from '@angular/common/http';
import { CoreBrapciModule } from './020_brapci/core-brapci.module';
//import { CoreBrapciComponent } from './200_brapci/core-brapci/core-brapci.component';

@NgModule({
  declarations: [
    AppComponent,
  ],
  imports: [
    AppRoutingModule,
    BrowserModule,
    HttpClientModule,
    NgbModule,
    ThemeModule,
    CoreBrapciModule,
  ],
  providers: [],
  bootstrap: [AppComponent],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class AppModule { }
