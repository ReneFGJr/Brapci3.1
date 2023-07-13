import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthorityMainComponent } from './page/authority-main/authority-main.component';

const routes: Routes = [
  {
    path: '', component: AuthorityMainComponent
  }
];

@NgModule({
  imports: [
    RouterModule.forChild(routes)
    ],
  exports: [RouterModule]
})
export class CoreRoutingModule { }
