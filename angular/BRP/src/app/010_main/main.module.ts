import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

// Roteamento
import { MainRoutingModule } from './main-routing.module';
import { MainHomeComponent } from './page/main-home.component';
import { AppModule } from '../app.module';
import { SearchFormComponent } from './page/search-form/search-form.component';
import { HomepageComponent } from './page/homepage/homepage.component';

/* Header */

@NgModule({
  declarations: [
    MainHomeComponent,
    SearchFormComponent,
    HomepageComponent,
  ],
  imports: [
    CommonModule,
    MainRoutingModule,
    AppModule
  ],
  schemas: [
    CUSTOM_ELEMENTS_SCHEMA
  ]
})
export class MainHomeModule { }
