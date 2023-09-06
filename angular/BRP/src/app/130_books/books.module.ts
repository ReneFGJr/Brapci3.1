import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BooksRoutingModule } from './books-routing.module';
import { BooksBannerHomeComponent } from './page/banner/banner.component';
import { ThemeModule } from '../010_thema/theme.module';
import { BooksVitrineComponent } from './page/vitrine/vitrine.component';
import { BookSubmitFormComponent } from './page/submit-form/submit-form.component';

@NgModule({
  declarations: [
    BooksBannerHomeComponent,
    BooksVitrineComponent,
    BookSubmitFormComponent,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [
    CommonModule,
    BooksRoutingModule,
    ThemeModule
  ]
})
export class BooksModule { }
