import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgForm } from '@angular/forms';

// Roteamento
import { MainRoutingModule } from './main-books-routing.module';

import { MainBooksComponent } from './page/main-books.component';
import { VitrineComponent } from './page/vitrine/vitrine.component';
import { NavbarBooksComponent } from './page/navbar/navbar.component';
import { MainBookAdminComponent } from './page/admin/main/main.component';
import { BookAddComponent } from './page/admin/book-add/book-add.component';

/* Header */

@NgModule({
  declarations: [
    MainBooksComponent,
    VitrineComponent,
    NavbarBooksComponent,
    MainBookAdminComponent,
    BookAddComponent,
  ],
  imports: [
    CommonModule,
    MainRoutingModule,
    NgModule
  ]
})
export class MainBooksModule { }
