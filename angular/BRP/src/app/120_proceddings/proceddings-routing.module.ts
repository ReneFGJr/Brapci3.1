import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { WelcomeProceedingsComponent } from './page/welcome-proceedings/welcome-proceedings.component';
import { ProceedingListComponent } from './page/proceeding-list/proceeding-list.component';
import { ProceedingIssuesComponent } from './page/proceeding-issues/proceeding-issues.component';
import { ProceedingsIssueViewComponent } from './page/proceedings-issue-view/proceedings-issue-view.component';

const routes: Routes = [
  {
    path: '',
    component: WelcomeProceedingsComponent,
    children: [
      { path: '', component: ProceedingListComponent },
      { path: 'issues/:id', component: ProceedingIssuesComponent },
      { path: 'issue/:id', component: ProceedingsIssueViewComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProceddingsRoutingModule { }
