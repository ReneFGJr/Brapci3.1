import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PopupRoutingModule } from './popup-routing.module';
import { PopUpIndexComponent } from './page/index/index.component';
import { ReactiveFormsModule } from '@angular/forms';


@NgModule({
  declarations: [PopUpIndexComponent],
  imports: [CommonModule, PopupRoutingModule, ReactiveFormsModule],
})
export class PopupModule {}
