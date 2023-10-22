import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RdfRoutingModule } from './rdf-routing.module';
import { MainRDFComponent } from './page/main/main.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ThemeModule } from '../010_thema/theme.module';
import { RDFClassesComponent } from './page/classes/classes.component';
import { RDFPropertiesComponent } from './page/properties/properties.component';
import { MainRdfOntologyComponent } from './page/main-ontology/main-ontology.component';


@NgModule({
  declarations: [
    MainRDFComponent,
    RDFClassesComponent,
    RDFPropertiesComponent,
    MainRdfOntologyComponent,
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule,
    RdfRoutingModule,
  ],
})
export class RdfModule {}
