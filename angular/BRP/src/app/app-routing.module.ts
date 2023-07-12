import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { Error404Component } from './000_header/error404/error404.component';
import { HomepageComponent } from './000_header/homepage/homepage.component';


const routes: Routes = [
  {
    path: '', component: HomepageComponent
  },
  {
    path: 'authority', loadChildren: () => import('./100_authority/mainauth.module').then(m => m.MainAuthoriryModule)
  },
  {
    path: 'main', loadChildren: () => import('./010_main/main.module').then(m => m.MainHomeModule)
  }
  ,
  {
    path: 'social', loadChildren: () => import('./001_auth/oauth.module').then(m => m.OauthModule)
  }
  ,
  {
    path: 'books', loadChildren: () => import('./110_books/main-books.module').then(m => m.MainBooksModule)
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
