import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { OaipmhRoutingModule } from './oaipmh-routing.module';
import { SourcesComponent } from './page/sources/sources.component';
import { WelcomeSourceComponent } from './page/welcome-source/welcome-source.component';
import { ThemeModule } from '../010_thema/theme.module';
import { MainOAIComponent } from './page/main/main.component';
import { MainSourcesComponent } from './page/main-sources/main-sources.component';
import { ViewSourceComponent } from './page/view-source/view-source.component';
import { ViewIssueComponent } from './page/view-issue/view-issue.component';
import { OaiButtonsComponent } from './apoio/oai-buttons/oai-buttons.component';


@NgModule({
  declarations: [
    SourcesComponent,
    WelcomeSourceComponent,
    MainOAIComponent,
    MainSourcesComponent,
    ViewSourceComponent,
    ViewIssueComponent,
    OaiButtonsComponent
  ],
  imports: [
    CommonModule,
    OaipmhRoutingModule,
    ThemeModule
  ]
})
export class OaipmhModule { }
