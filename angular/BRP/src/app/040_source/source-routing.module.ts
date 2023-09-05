import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SourceComponent } from './page/source/source.component';
import { SourceViewComponent } from './page/source-view/source-view.component';

const routes: Routes = [
  {
    path: '', component: SourceComponent, children:
    [
      { path: 'view/:id', component: SourceViewComponent }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SourceRoutingModule { }
