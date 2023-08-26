import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ProceddingsRoutingModule } from './proceddings-routing.module';
import { WelcomeProceedingsComponent } from './page/welcome-proceedings/welcome-proceedings.component';
import { ThemeModule } from '../010_thema/theme.module';
import { ProceedingListComponent } from './page/proceeding-list/proceeding-list.component';
import { ProceedingIssuesComponent } from './page/proceeding-issues/proceeding-issues.component';


@NgModule({
  declarations: [
    WelcomeProceedingsComponent,
    ProceedingListComponent,
    ProceedingIssuesComponent
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [
    CommonModule,
    ProceddingsRoutingModule,
    ThemeModule
  ]
})
export class ProceddingsModule { }
