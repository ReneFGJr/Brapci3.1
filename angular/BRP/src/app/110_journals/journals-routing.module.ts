import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainJournalComponent } from './page/main-journal/main-journal.component';
import { JournalWelcomeComponent } from './page/journal-welcome/journal-welcome.component';
import { JournalViewComponent } from './page/journal-view/journal-view.component';

const routes: Routes = [
  {
    path: '', component: MainJournalComponent, children:
    [
        { path: '', component: JournalWelcomeComponent },
        { path: 'view/:id', component: JournalViewComponent },
    ]
  }
];

@NgModule({
  imports: [
    RouterModule.forChild(routes)
    ],
  exports: [RouterModule]
})
export class CoreRoutingModule {
  [x: string]: any;
}
