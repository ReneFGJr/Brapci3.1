import { NgModule, CUSTOM_ELEMENTS_SCHEMA, LOCALE_ID } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ReactiveFormsModule } from '@angular/forms';

/* Modules */
import { ThemeModule } from './010_thema/theme.module';

import { CoreBrapciModule } from './020_brapci/core-brapci.module';

import ptBr from '@angular/common/locales/pt';
import { registerLocaleData } from '@angular/common';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { SourceAddComponent } from './040_source/page/source-add/source-add.component';
import { HomeComponent } from './130_books/page/home/home.component';

registerLocaleData(ptBr);

//import { RouterModule } from '@angular/router';
//import { CoreBrapciComponent } from './200_brapci/core-brapci/core-brapci.component';

@NgModule({
  declarations: [AppComponent, SourceAddComponent, HomeComponent],
  imports: [
    AppRoutingModule,
    BrowserModule,
    HttpClientModule,
    NgbModule,
    ThemeModule,
    CoreBrapciModule,
    BrowserAnimationsModule,
    ReactiveFormsModule,
  ],
  providers: [{ provide: LOCALE_ID, useValue: 'pt-BR' }],
  bootstrap: [AppComponent],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
})
export class AppModule {}
