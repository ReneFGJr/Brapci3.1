import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

/********** Module */
import { CoreRoutingModule } from './authotity-core-routing.module';
import { ThemeModule } from '../010_thema/theme.module';

/********** Componente */
import { AuthorityMainComponent } from './page/authority-main/authority-main.component';

import { BannerAuthorityComponent } from './page/banner/banner.component';
import { SearchAuthorityComponent } from './page/search/search.component';
import { SearchAuthorityHomeComponent } from './page/search-home/search-home.component';

import { ItemListAuthorityComponent } from './page/item-list/item-list.component';
import { ItemViewPersonComponent } from './page/item-view/item-view-person/item-view-person.component';
import { ItemViewComponent } from './page/item-view/item-view.component';
import { ItemViewInstitutionComponent } from './page/item-view/item-view-institution/item-view-institution.component';



@NgModule({
  declarations: [
        AuthorityMainComponent,
        BannerAuthorityComponent,
        SearchAuthorityComponent,
        SearchAuthorityHomeComponent,
        ItemListAuthorityComponent,
        ItemViewPersonComponent,
        ItemViewComponent,
        ItemViewInstitutionComponent,
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
      ItemListAuthorityComponent,
      SearchAuthorityComponent
    ]
})
export class Core100Module { }
