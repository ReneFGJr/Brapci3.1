import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainAuthorityComponent } from './main/main.component';

const routes: Routes = [
  {
    path: '', component: MainAuthorityComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MainauthRoutingModule { }
