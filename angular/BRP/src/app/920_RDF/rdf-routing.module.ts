import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainRDFComponent } from './page/main/main.component';
import { MainRdfOntologyComponent } from './page/main-ontology/main-ontology.component';
import { RDFVComponent } from './page/v/v.component';
import { RDFViewdataComponent } from './page/viewdata/viewdata.component';
import { FormComponent } from './page/form/form.component';

const routes: Routes = [
  {
    path: '',
    component: MainRDFComponent,
    children: [
      { path: '', component: MainRdfOntologyComponent },
      { path: 'v/:id', component: RDFVComponent },
      { path: 'viewdata/:id', component: RDFViewdataComponent },
      { path: 'form/:id', component: FormComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RdfRoutingModule { }
