import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './page/home/home.component';
import { BooksVitrineComponent } from './page/vitrine/vitrine.component';
import { BookSubmitFormComponent } from './page/submit-form/submit-form.component';

const routes: Routes = [
  { path: '', component: HomeComponent, children:
  [
    { path: '', component: BooksVitrineComponent},
    { path: 'submit', component: BookSubmitFormComponent}
  ]}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BooksRoutingModule { }
