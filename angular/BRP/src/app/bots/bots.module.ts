import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BotsRoutingModule } from './bots-routing.module';
import { BotsWelcomeComponent } from './welcome/welcome.component';
import { ProcessComponent } from './page/process/process.component';
import { RobotiComponent } from './widgat/roboti/roboti.component';


@NgModule({
  declarations: [BotsWelcomeComponent, ProcessComponent, RobotiComponent],
  imports: [CommonModule, BotsRoutingModule],
})
export class BotsModule {}
