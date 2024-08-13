import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PopupRoutingModule } from './popup-routing.module';
import { PopUpIndexComponent } from './page/index/index.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RdfFormComponent } from './page/rdf-form/rdf-form.component';
import { DeleteRDFComponent } from './page/delete/delete.component';


@NgModule({
  declarations: [PopUpIndexComponent, RdfFormComponent, DeleteRDFComponent],
  imports: [CommonModule, PopupRoutingModule, ReactiveFormsModule],
})
export class PopupModule {}
