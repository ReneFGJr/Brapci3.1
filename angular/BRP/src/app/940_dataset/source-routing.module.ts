import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeDatasetComponent } from './page/home/home.component';

const routes: Routes = [{ path: '', component: HomeDatasetComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SourceRoutingModule { }
