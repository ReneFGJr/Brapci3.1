import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainComponent } from './000_main/main/main.component';

const routes: Routes = [
  { path: '', component: MainComponent },
  { path: 'authority', loadChildren: () => import('../100_authority/authotity-core.module').then(m => m.Core100Module) } ,
  { path: 'social', loadChildren: () => import('../001_auth/oauth.module').then(m => m.OauthModule) }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CoreRoutingModule {
  constructor() {}

  ngInitOn()
    {
      console.log('init')
    }

}
