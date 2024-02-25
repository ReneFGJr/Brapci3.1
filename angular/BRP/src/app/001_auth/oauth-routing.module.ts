import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainOauthComponent } from './page/oauth/main/main.component';
import { LogoutComponent } from './page/oauth/main/logout/logout.component';
import { LoginComponent } from './page/oauth/main/login/login.component';
import { PerfilComponent } from './page/perfil/perfil.component';
import { ChangePasswordComponent } from './page/oauth/change-password/change-password.component';

const routes: Routes = [
  {
    path: '',
    component: MainOauthComponent,
    children: [
      { path: '', component: LoginComponent },
      { path: 'signin', component: LoginComponent },
      { path: 'logout', component: LogoutComponent },
      { path: 'perfil', component: PerfilComponent },
      { path: 'pass/:id', component: ChangePasswordComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OauthRoutingModule { }
