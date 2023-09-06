import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SourceRoutingModule } from './source-routing.module';
import { SourceComponent } from './page/source/source.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ThemeModule } from '../010_thema/theme.module';

@NgModule({
  declarations: [SourceComponent],
  imports: [
    CommonModule,
    SourceRoutingModule,
    ReactiveFormsModule,
    FormsModule,
    RouterModule,
    ThemeModule,
  ],
})
export class SourceModule {}
