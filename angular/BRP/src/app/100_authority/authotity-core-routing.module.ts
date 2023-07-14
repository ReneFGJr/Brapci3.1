import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthorityMainComponent } from './page/authority-main/authority-main.component';
import { ItemViewComponent } from './page/item-view/item-view.component';
import { SearchAuthorityHomeComponent } from './page/search-home/search-home.component';

const routes: Routes = [
  { path: '', component: AuthorityMainComponent, children:
    [
      { path: '', component: SearchAuthorityHomeComponent },
      { path: 'person/:id', component: ItemViewComponent }
    ]
  }
];

@NgModule({
  imports: [
    RouterModule.forChild(routes)
    ],
  exports: [RouterModule]
})
export class CoreRoutingModule {
  [x: string]: any;
}
