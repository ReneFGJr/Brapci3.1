import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { PopUpIndexComponent } from './page/index/index.component';

const routes: Routes = [
  {
    path: '',
    component: PopUpIndexComponent,
    children: [
      { path: '', component: PopUpIndexComponent },
      { path: 'v/:id', component: PopUpIndexComponent },
      { path: 'viewdata/:id', component: PopUpIndexComponent },
      { path: 'form/:id', component: PopUpIndexComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PopupRoutingModule { }
