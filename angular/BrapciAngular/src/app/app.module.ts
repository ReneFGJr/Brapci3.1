import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { NavbarComponent } from './modules/headers/navbar/navbar.component';
import { FootComponent } from './modules/headers/foot/foot.component';
import { LogoComponent } from './modules/headers/logo/logo.component';
import { FilterComponent } from './modules/search/filter/filter.component';
import { FormComponent } from './modules/search/form/form.component';
import { YearComponent } from './modules/search/filter/year/year.component';
import { CollectionsComponent } from './modules/search/filter/collections/collections.component';
import { DataCollectionsComponent } from './data/filters/data-collections/data-collections.component';
import { DataYearComponent } from './data/filters/data-year/data-year.component';

@NgModule({
  declarations: [
    AppComponent,
    NavbarComponent,
    FootComponent,
    LogoComponent,
    FilterComponent,
    FormComponent,
    YearComponent,
    CollectionsComponent,
    DataCollectionsComponent,
    DataYearComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
