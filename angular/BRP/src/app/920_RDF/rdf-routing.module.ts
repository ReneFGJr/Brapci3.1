import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainRDFComponent } from './page/main/main.component';
import { MainRdfOntologyComponent } from './page/main-ontology/main-ontology.component';

const routes: Routes = [
  {
    path: '',
    component: MainRDFComponent,
    children: [{ path: '', component: MainRdfOntologyComponent }],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RdfRoutingModule { }
