import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PqRoutingModule } from './pq-routing.module';
import { MainComponent } from './page/main/main.component';
import { WelcomeComponent } from './page/welcome/welcome.component';


@NgModule({
  declarations: [MainComponent, WelcomeComponent],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [CommonModule, PqRoutingModule],
})
export class PqModule {}
