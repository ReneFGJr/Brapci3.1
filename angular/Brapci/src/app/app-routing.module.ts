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

const APProutes: Routes = [


  { path: 'social', component: OauthComponent, children:
      [
      { path: 'password', component: UserChangePasswordComponent },
      { path: 'perfil', component: UserPerfilComponent }
      ]
  },
  { path: 'main', component: HomeComponent,
    children:
    [
      { path: '', component: MainComponent },
      { path: 'about', component: AboutComponent }
    ] },
  {
    path: 'books', component: BrapciHomeLivrosComponent,
    children:
      [
        { path: '', component: BrapciHomeLivrosComponent },
        { path: 'about', component: AboutComponent }
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
