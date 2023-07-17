import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CoreRoutingModule } from './core-routing.module';
import { MainComponent } from './000_main/main/main.component';
import { ThemeModule } from '../010_thema/theme.module';
import { CoreBrapciModule } from '../020_brapci/core-brapci.module';

@NgModule({
  declarations: [
    MainComponent
  ],
  imports: [
    CommonModule,
    CoreRoutingModule,
    ThemeModule,
    CoreBrapciModule
  ]
})
export class CoreModule { }
