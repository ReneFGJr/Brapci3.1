import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { BotsWelcomeComponent } from './welcome/welcome.component';
import { ProcessComponent } from './page/process/process.component';
import { PersonComponent } from './page/person/person.component';
import { ArticleComponent } from './page/article/article.component';

const routes: Routes = [
  { path: '', component: BotsWelcomeComponent },
  { path: 'process', component: ProcessComponent },
  { path: 'article', component: ArticleComponent },
  { path: 'person', component: PersonComponent },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BotsRoutingModule { }
