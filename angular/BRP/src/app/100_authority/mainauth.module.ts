import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MainauthRoutingModule } from './mainauth-routing.module';
import { MainAuthorityComponent } from './main/main.component';
import { AppModule } from '../app.module';

@NgModule({
  declarations: [
    MainAuthorityComponent
  ],
  imports: [
    CommonModule,
    MainauthRoutingModule,
  ],
  schemas: [
    CUSTOM_ELEMENTS_SCHEMA
  ]
})
export class MainAuthoriryModule { }
