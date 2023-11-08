import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BrapciToolsRoutingModule } from './brapci-tools-routing.module';
import { ToolsMainComponent } from './main/tools-main/tools-main.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ThemeModule } from '../010_thema/theme.module';
import { ToolsIconsComponent } from './page/main/tools-icons/tools-icons.component';


@NgModule({
  declarations: [ToolsMainComponent, ToolsIconsComponent],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule,
    BrapciToolsRoutingModule,
  ],
})
export class BrapciToolsModule {}
