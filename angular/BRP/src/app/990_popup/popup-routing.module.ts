import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { PopUpIndexComponent } from './page/index/index.component';
import { RdfFormComponent } from './page/rdf-form/rdf-form.component';
import { DeleteRDFComponent } from './page/delete/delete.component';

const routes: Routes = [
  {
    path: '',
    component: PopUpIndexComponent,
    children: [
      { path: 'rdf/add/:id/:prop', component: RdfFormComponent },
      { path: 'rdf/delete/:id/:prop', component: DeleteRDFComponent },
      //https://brapci.inf.br/#/popup/rdf/delete/1046087
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PopupRoutingModule { }
