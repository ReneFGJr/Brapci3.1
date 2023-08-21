import { Component, NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { WelcomeSourceComponent } from './page/welcome-source/welcome-source.component';
import { MainOAIComponent } from './page/main/main.component';
import { MainSourcesComponent } from './page/main-sources/main-sources.component';
import { ViewSourceComponent } from './page/view-source/view-source.component';
import { ViewIssueComponent } from './page/view-issue/view-issue.component';

const routes: Routes = [
  {path: '', component: WelcomeSourceComponent, children:
  [
    {path: '', component: MainOAIComponent },
    {path: 'row', component: MainSourcesComponent },
    {path: 'oai', component: MainOAIComponent },
     { path: 'view/:id', component: ViewSourceComponent },
     { path: 'issue/:id', component: ViewIssueComponent }

  ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OaipmhRoutingModule { }
