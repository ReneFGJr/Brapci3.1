import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

// Roteamento
import { OauthRoutingModule } from './oauth-routing.module';

//Main
import { MainOauthComponent } from './page/oauth/main/main.component';
import { LogoutComponent } from './page/oauth/main/logout/logout.component';
import { LoginComponent } from './page/oauth/main/login/login.component';
import { PerfilComponent } from './page/perfil/perfil.component';
import { ThemeModule } from '../010_thema/theme.module';
import { ChangePasswordComponent } from './page/oauth/change-password/change-password.component';

@NgModule({
  declarations: [
    MainOauthComponent,
    LogoutComponent,
    LoginComponent,
    PerfilComponent,
    ChangePasswordComponent,
  ],
  imports: [
    CommonModule,
    OauthRoutingModule,
    ReactiveFormsModule,
    ThemeModule,
    FormsModule,
  ],
  exports: [],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
})
export class OauthModule {}
