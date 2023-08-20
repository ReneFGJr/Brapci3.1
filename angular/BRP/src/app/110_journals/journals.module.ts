import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

import { MainJournalComponent } from './page/main-journal/main-journal.component';
import { ThemeModule } from '../010_thema/theme.module';
import { CoreRoutingModule } from './journals-routing.module';
import { JournalWelcomeComponent } from './page/journal-welcome/journal-welcome.component';
import { JournalViewComponent } from './page/journal-view/journal-view.component';
import { JournalBannerComponent } from './page/journal-banner/journal-banner.component';

@NgModule({
  declarations: [
    MainJournalComponent,
    JournalWelcomeComponent,
    JournalViewComponent,
    JournalBannerComponent
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [
    CommonModule,
    ThemeModule,
    CoreRoutingModule,
    NgbModule,
    FormsModule,
    ReactiveFormsModule,
  ],
})
export class JournalsModule { }
