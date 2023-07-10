import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm, ReactiveFormsModule } from '@angular/forms';

// Roteamento
import { MainRoutingModule } from './main-books-routing.module';


/* Header */

@NgModule({
  declarations: [
  ],
  imports: [
    CommonModule,
    MainRoutingModule,
    NgModule,
    NgForm,
    FormsModule,
    ReactiveFormsModule
  ]
})
export class MainBooksModule { }
