import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AppComponent } from './app.component';
import { CpfComponent } from './page/form/cpf/cpf.component';
import { EventoComponent } from './page/evento/evento.component';

const routes: Routes = [
  {path: '', component: AppComponent, children:
  [
    {path: '', component: EventoComponent},
    {path: 'inscricao', component: CpfComponent}
  ]}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
