import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainHomeComponent } from './page/main-home.component';
import { WelcomeComponent } from './page/welcome/welcome.component';


const routes: Routes = [
  {
    path: '', component: MainHomeComponent, children:
      [
        { path: '', component: WelcomeComponent }
      ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MainRoutingModule { }
