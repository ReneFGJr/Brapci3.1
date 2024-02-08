import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { PopUpIndexComponent } from './page/index/index.component';
import { RdfFormComponent } from './page/rdf-form/rdf-form.component';

const routes: Routes = [
  {
    path: '',
    component: PopUpIndexComponent,
    children: [
      { path: 'rdf/add/:id/:prop', component: RdfFormComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PopupRoutingModule { }
