import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ThemeNavbarComponent } from './header/theme-navbar/theme-navbar.component';
import { ThemeHeaderComponent } from './header/theme-header/theme-header.component';
import { ThemeFooterComponent } from './header/theme-footer/theme-footer.component';
import { ThemeDeniedComponent } from './header/theme-denied/theme-denied.component';
import { RouterModule } from '@angular/router';
import { LoginIconeComponent } from './widgat/login-icone/login-icone.component';
import { BrapciLivrosComponent } from './header/brapci-livros/brapci-livros.component';
import { LikesComponent } from './widgat/likes/likes.component';
import { DownloadsComponent } from './widgat/downloads/downloads.component';
import { ViewsComponent } from './widgat/views/views.component';

@NgModule({
  declarations: [
    ThemeNavbarComponent,
    ThemeHeaderComponent,
    ThemeFooterComponent,
    ThemeDeniedComponent,
    BrapciLivrosComponent,
    LoginIconeComponent,
    BrapciLivrosComponent,
    LikesComponent,
    DownloadsComponent,
    ViewsComponent,
  ],
  imports: [CommonModule, RouterModule],
  exports: [
    ThemeHeaderComponent,
    ThemeFooterComponent,
    ThemeNavbarComponent,
    BrapciLivrosComponent,
    ViewsComponent,
    LikesComponent,
    DownloadsComponent,
  ],
})
export class ThemeModule {}
