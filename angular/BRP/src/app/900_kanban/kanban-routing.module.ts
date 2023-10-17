import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { KanBanMainComponent } from './page/main/main.component';

const routes: Routes = [{ path: '', component: KanBanMainComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class KanbanRoutingModule { }
