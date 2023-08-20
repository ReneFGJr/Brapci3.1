import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { WelcomeProceedingsComponent } from './page/welcome-proceedings/welcome-proceedings.component';

const routes: Routes = [
  { path: '', component: WelcomeProceedingsComponent },

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProceddingsRoutingModule { }
