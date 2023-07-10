import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

// Roteamento
import { OauthRoutingModule } from './oauth-routing.module';

//Main
import { MainOauthComponent } from './page/oauth/main/main.component';
import { LogoutComponent } from './page/oauth/main/logout/logout.component';
import { LoginComponent } from './page/oauth/main/login/login.component';
import { ReactiveFormsModule } from '@angular/forms';


@NgModule({
  declarations: [
    MainOauthComponent,
    LogoutComponent,
    LoginComponent
  ],
  imports: [
    CommonModule,
    OauthRoutingModule,
    ReactiveFormsModule
  ]
})
export class OauthModule { }
