import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { IndexEventComponent } from './page/index/index.component';

const routes: Routes = [
  {
    path: '',
    component: IndexEventComponent,
    children: [{ path: '', component: IndexEventComponent }],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EventsRoutingModule { }
