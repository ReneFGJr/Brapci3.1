import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainOauthComponent } from './page/oauth/main/main.component';
import { LogoutComponent } from './page/oauth/main/logout/logout.component';
import { LoginComponent } from './page/oauth/main/login/login.component';

const routes: Routes = [
  {
    path: '', component: MainOauthComponent, children:
    [
        { path: '', component: LoginComponent },
        { path: 'logout', component: LogoutComponent }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OauthRoutingModule { }
