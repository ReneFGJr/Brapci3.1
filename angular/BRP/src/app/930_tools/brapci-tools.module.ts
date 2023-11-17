import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BrapciToolsRoutingModule } from './brapci-tools-routing.module';
import { ToolsMainComponent } from './main/tools-main/tools-main.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ThemeModule } from '../010_thema/theme.module';
import { ToolsIconsComponent } from './page/main/tools-icons/tools-icons.component';
import { Txt4netComponent } from './tools/txt4net/txt4net.component';
import { Txt4matrixComponent } from './tools/txt4matrix/txt4matrix.component';
import { Txt4charComponent } from './tools/txt4char/txt4char.component';
import { TxtChangeComponent } from './tools/txt-change/txt-change.component';
import { Txt4gephiComponent } from './tools/txt4gephi/txt4gephi.component';
import { FormFileInputComponent } from './widget/form-file-input/form-file-input.component';
import { TextFormComponent } from './widget/text-form/text-form.component';


@NgModule({
  declarations: [ToolsMainComponent, ToolsIconsComponent, Txt4netComponent, Txt4matrixComponent, Txt4charComponent, TxtChangeComponent, Txt4gephiComponent, FormFileInputComponent, TextFormComponent],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule,
    BrapciToolsRoutingModule,
  ],
})
export class BrapciToolsModule {}
