import { NgModule } from '@angular/core';
import { NavigationEnd, RouterModule, Routes } from '@angular/router';
import { OauthComponent } from './001_auth/oauth/oauth.component';
import { HomeComponent } from './002_main/home/home.component';
import { AboutComponent } from './002_main/home/about/about.component';
import { MainComponent } from './002_main/home/main/main.component';
import { UserChangePasswordComponent } from './001_auth/page/user-change-password/user-change-password.component';
import { UserPerfilComponent } from './001_auth/page/user-perfil/user-perfil.component';
import { BrapciHomeLivrosComponent } from './100_brapci_livros/page/brapci-home-livros/brapci-home-livros.component';
import { AuthorityMainComponent } from './110_brapci_autoridades/authority-main/authority-main.component';
import { BookServicesComponent } from './110_find/find/admin/book-services/book-services.component';
import { LivroViewComponent } from './100_brapci_livros/page/livro-view/livro-view.component';
import { LivrosMainComponent } from './100_brapci_livros/livros-main/livros-main.component';
import { WelcomeComponent } from './002_main/page/welcome/welcome.component';
import { LivroVitrineComponent } from './100_brapci_livros/page/livro-vitrine/livro-vitrine.component';

const APProutes: Routes = [

  {
    path: 'social', component: OauthComponent, children:
      [
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
        { path: '', component: BrapciHomeLivrosComponent },
        { path: 'about', component: AboutComponent },
        { path: 'view/:id', component: LivroViewComponent },
        { path: 'admin', component: BookServicesComponent },
      ]
  },
  {
    path: 'authotity', component: AuthorityMainComponent,
    children:
      [
        { path: '', component: AuthorityMainComponent },
        { path: 'about', component: AboutComponent }
      ]
  },

];

@NgModule({
  imports: [RouterModule.forRoot(APProutes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
