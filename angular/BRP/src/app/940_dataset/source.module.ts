import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SourceRoutingModule } from './source-routing.module';
import { HomeDatasetComponent } from './page/home/home.component';


@NgModule({
  declarations: [
    HomeDatasetComponent
  ],
  imports: [
    CommonModule,
    SourceRoutingModule
  ]
})
export class SourceModule { }
