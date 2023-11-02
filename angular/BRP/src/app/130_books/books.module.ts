import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BooksRoutingModule } from './books-routing.module';
import { BooksBannerHomeComponent } from './page/banner/banner.component';
import { ThemeModule } from '../010_thema/theme.module';
import { BooksVitrineComponent } from './page/vitrine/vitrine.component';
import { BookSubmitFormComponent } from './page/submit-form/submit-form.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BookTermComponent } from './page/term/term.component';
import { UploadFileComponent } from './upload-file.component';
import { BookSubmitBottomComponent } from './gadget/book-submit-bottom/book-submit-bottom.component';
import { VitrineBooksComponent } from './page/vitrine-books/vitrine-books.component';
import { VitrineCategoriesComponent } from './page/vitrine-categories/vitrine-categories.component';
import { VitrineSearchComponent } from './page/vitrine-search/vitrine-search.component';

@NgModule({
  declarations: [
    BooksBannerHomeComponent,
    BooksVitrineComponent,
    BookSubmitFormComponent,
    BookTermComponent,
    UploadFileComponent,
    BookSubmitBottomComponent,
    VitrineBooksComponent,
    VitrineCategoriesComponent,
    VitrineSearchComponent,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [
    CommonModule,
    BooksRoutingModule,
    ThemeModule,
    FormsModule,
    ReactiveFormsModule,
  ],
})
export class BooksModule {}
