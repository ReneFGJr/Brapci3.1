import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HeaderComponent } from './000_header/header/header.component';
import { FooterComponent } from './000_header/footer/footer.component';
import { NavbarComponent } from './000_header/navbar/navbar.component';
import { HomeComponent } from './002_main/home/home.component';
import { AboutComponent } from './002_main/home/about/about.component';
import { MainComponent } from './002_main/home/main/main.component';
import { FormsModule } from '@angular/forms';
import { OauthModule } from './001_auth/OauthModule';
import { UserPerfilComponent } from './001_auth/page/user-perfil/user-perfil.component';
import { UserChangePasswordComponent } from './001_auth/page/user-change-password/user-change-password.component';
import { BrapciHomeLivrosComponent } from './100_brapci_livros/page/brapci-home-livros/brapci-home-livros.component';
import { LivrosMainComponent } from './100_brapci_livros/livros-main/livros-main.component';
import { ViewAuthorityComponent } from './110_brapci_autoridades/page/view-authority/view-authority.component';
import { NavbarBooksComponent } from './100_brapci_livros/header/navbar-books/navbar-books.component';
import { LivrosBannersComponent } from './100_brapci_livros/page/livros-banners/livros-banners.component';
import { LivroVitrineComponent } from './100_brapci_livros/page/livro-vitrine/livro-vitrine.component';
import { FindComponent } from './110_find/find/find.component';
import { BookServicesComponent } from './110_find/find/admin/book-services/book-services.component';

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    FooterComponent,
    NavbarComponent,
    HomeComponent,
    AboutComponent,
    MainComponent,
    UserPerfilComponent,
    UserChangePasswordComponent,
    BrapciHomeLivrosComponent,
    LivrosMainComponent,
    ViewAuthorityComponent,
    NavbarBooksComponent,
    LivrosBannersComponent,
    LivroVitrineComponent,
    FindComponent,
    BookServicesComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    OauthModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
