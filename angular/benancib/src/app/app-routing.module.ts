import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainComponent } from './page/main/main.component';
import { ViewComponent } from './page/view/view.component';
import { AboutComponent } from './page/about/about.component';

const routes: Routes = [
  { path: '', component: MainComponent , children:
    [
      { path: 'view', component: ViewComponent },
      { path: 'about', component: AboutComponent },
    ]
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
