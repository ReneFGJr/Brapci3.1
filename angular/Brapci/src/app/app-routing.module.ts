/* Angular */
import { NgModule } from '@angular/core';

/* Guard */

import { Error404Component } from './000_header/error404/error404.component';

/* Aplicacao 001 */
import { UserChangePasswordComponent } from './001_auth/page/user-change-password/user-change-password.component';
import { UserPerfilComponent } from './001_auth/page/user-perfil/user-perfil.component';
import { UserLoginComponent } from './001_auth/page/user-login/user-login.component';

/* Aplicacao 002 */
import { HomeComponent } from './002_main/home/home.component';
import { AboutComponent } from './002_main/home/about/about.component';
import { MainComponent } from './002_main/home/main/main.component';
import { WelcomeComponent } from './002_main/page/welcome/welcome.component';

/* Aplicacao 100 */
import { BrapciHomeLivrosComponent } from './100_brapci_livros/page/brapci-home-livros/brapci-home-livros.component';
import { LivroViewComponent } from './100_brapci_livros/page/livro-view/livro-view.component';
import { LivrosMainComponent } from './100_brapci_livros/livros-main/livros-main.component';
import { LivroVitrineComponent } from './100_brapci_livros/page/livro-vitrine/livro-vitrine.component';

/* Aplicacao 110 */
import { BookServicesComponent } from './110_find/find/admin/book-services/book-services.component';
import { BookAddComponent } from './110_find/find/admin/book-add/book-add.component';
import { BookEditComponent } from './110_find/find/admin/book-edit/book-edit.component';

/* Aplicacao 120 */
import { AuthorityMainComponent } from './120_brapci_autoridades/authority-main/authority-main.component';
import { guardOauthGuard } from './001_auth/guard/guard-oauth.guard';

const APProutes: Routes = [
  {
    path: 'social', component: OauthComponent, children:
      [
        { path: '', component: OauthComponent },
        { path: 'login', component: UserLoginComponent },
        { path: 'password', component: UserChangePasswordComponent },
        { path: 'perfil', component: UserPerfilComponent }
      ]
  },

  {
    path: '', component: MainComponent,
    children:
      [
        { path: '', component: WelcomeComponent },
        { path: 'about', component: AboutComponent }
      ]
  },

  /**************************************************** LIVROS */
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
  },
  {
    path: 'authotity', component: AuthorityMainComponent,
    children:
      [
        { path: '', component: AuthorityMainComponent },
        { path: 'about', component: AboutComponent }
      ]
  },{
    path: '**', component: Error404Component
  }

];

@NgModule({
  imports: [RouterModule.forRoot(APProutes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
