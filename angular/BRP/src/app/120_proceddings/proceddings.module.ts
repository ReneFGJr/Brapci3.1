import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ProceddingsRoutingModule } from './proceddings-routing.module';
import { WelcomeProceedingsComponent } from './page/welcome-proceedings/welcome-proceedings.component';
import { ThemeModule } from '../010_thema/theme.module';
import { ProceedingListComponent } from './page/proceeding-list/proceeding-list.component';
import { ProceedingIssuesComponent } from './page/proceeding-issues/proceeding-issues.component';
import { ProceedingsIssueViewComponent } from './page/proceedings-issue-view/proceedings-issue-view.component';
import { CoreBrapciModule } from '../020_brapci/core-brapci.module';
import { SearchSimpleFormComponent } from './search/search-simple-form/search-simple-form.component';
import { ReactiveFormsModule } from '@angular/forms';

@NgModule({
  declarations: [
    WelcomeProceedingsComponent,
    ProceedingListComponent,
    ProceedingIssuesComponent,
    ProceedingsIssueViewComponent,
    SearchSimpleFormComponent,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [
    CommonModule,
    ProceddingsRoutingModule,
    ThemeModule,
    CoreBrapciModule,
    ReactiveFormsModule,
  ],
})
export class ProceddingsModule {}
