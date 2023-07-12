import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainHomeComponent } from './page/main-home.component';
import { HomepageComponent } from '../000_header/homepage/homepage.component';


const routes: Routes = [
  {
    path: '', component: MainHomeComponent, children:
      [
        { path: '', component: HomepageComponent }
      ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MainRoutingModule { }
