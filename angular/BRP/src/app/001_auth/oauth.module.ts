import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';

// Roteamento
import { OauthRoutingModule } from './oauth-routing.module';

//Main
import { MainOauthComponent } from './page/oauth/main/main.component';
import { LogoutComponent } from './page/oauth/main/logout/logout.component';
import { LoginComponent } from './page/oauth/main/login/login.component';



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
