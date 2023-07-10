import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

// Roteamento
import { MainRoutingModule } from './main-routing.module';
import { MainHomeComponent } from './page/main-home.component';
import { WelcomeComponent } from './page/welcome/welcome.component';
import { AppModule } from '../app.module';

/* Header */

@NgModule({
  declarations: [
    MainHomeComponent,
    WelcomeComponent,
  ],
  imports: [
    CommonModule,
    MainRoutingModule,
    AppModule
  ]
})
export class MainHomeModule { }
