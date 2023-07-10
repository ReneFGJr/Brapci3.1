import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { WelcomeComponent } from './000_header/welcome/welcome.component';
import { Error404Component } from './000_header/error404/error404.component';


const routes: Routes = [
  {
    path: '', component: WelcomeComponent
  }
  ,
  {
    path: 'social', loadChildren: () => import('./001_auth/oauth.module').then(m => m.OauthModule)
  }

  /************************** Erros 404 ****/
  , {
    path: '**', component: Error404Component
  }
  /**************************************************** LIVROS */
  /*
    {
    path: 'books', component: LivrosMainComponent,
    children:
      [
        { path: 'admin', component: BookServicesComponent },
        { path: 'admin/isbn/add', component: BookAddComponent },
        { path: 'admin/isbn/edit/:id', component: BookEditComponent }
      ], canActivate: [guardOauthGuard]
  },
  {
    path: 'books', component: LivrosMainComponent,
    children:
      [
        { path: '', component: BrapciHomeLivrosComponent },
        { path: 'about', component: AboutComponent },
        { path: 'view/:id', component: LivroViewComponent },
      ]
  }
  */
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {

}
