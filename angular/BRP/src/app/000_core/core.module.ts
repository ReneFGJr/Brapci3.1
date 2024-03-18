import { CookieService } from 'ngx-cookie-service';
import { NgModule } from '@angular/core';
import { CommonModule, HashLocationStrategy, LocationStrategy } from '@angular/common';

import { CoreRoutingModule } from './core-routing.module';
import { MainComponent } from './000_main/main/main.component';
import { ThemeModule } from '../010_thema/theme.module';
import { CoreBrapciModule } from '../020_brapci/core-brapci.module';
import { RouterModule } from '@angular/router';
import { ManutenceComponent } from './000_main/manutence/manutence.component';

@NgModule({
  declarations: [MainComponent, ManutenceComponent],
  imports: [
    CommonModule,
    CoreRoutingModule,
    ThemeModule,
    CoreBrapciModule,
    RouterModule,
  ],
  providers: [
    CookieService ,
    {
      provide: LocationStrategy,
      useClass: HashLocationStrategy,
    },
  ],
})
export class CoreModule {}
