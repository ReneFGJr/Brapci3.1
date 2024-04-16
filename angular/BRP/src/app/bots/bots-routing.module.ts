import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { BotsWelcomeComponent } from './welcome/welcome.component';
import { ProcessComponent } from './page/process/process.component';

const routes: Routes = [
  { path: '', component: BotsWelcomeComponent },
  { path: 'process', component: ProcessComponent },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BotsRoutingModule { }
