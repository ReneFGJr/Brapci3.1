import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SourceRoutingModule } from './source-routing.module';
import { SourceComponent } from './page/source/source.component';


@NgModule({
  declarations: [
    SourceComponent
  ],
  imports: [
    CommonModule,
    SourceRoutingModule
  ]
})
export class SourceModule { }
