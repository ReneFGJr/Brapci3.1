import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CoreRoutingModule } from './authotity-core-routing.module';
import { AuthorityMainComponent } from './page/authority-main/authority-main.component';
import { ThemeModule } from '../010_thema/theme.module';
import { BrowserModule } from '@angular/platform-browser';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

@NgModule({
  declarations: [
        AuthorityMainComponent,
  ],
  imports: [
    CommonModule,
    ThemeModule,
    CoreRoutingModule,
    NgbModule,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  exports:
    [

    ]
})
export class Core100Module { }
