import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CoreRoutingModule } from './authotity-core-routing.module';
import { AuthorityMainComponent } from './page/authority-main/authority-main.component';
import { ThemeModule } from '../010_thema/theme.module';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { BannerAuthorityComponent } from './page/banner/banner.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { SearchAuthorityComponent } from './page/search/search.component';

@NgModule({
  declarations: [
        AuthorityMainComponent,
        BannerAuthorityComponent,
        SearchAuthorityComponent,
  ],
  imports: [
    CommonModule,
    ThemeModule,
    CoreRoutingModule,
    NgbModule,
    FormsModule,
    ReactiveFormsModule,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  exports:
    [

    ]
})
export class Core100Module { }
