import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AppComponent } from './app.component';
import { CpfComponent } from './page/form/cpf/cpf.component';

const routes: Routes = [
  {path: '', component: AppComponent, children:
  [
    {path: '', component: CpfComponent}
  ]}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
