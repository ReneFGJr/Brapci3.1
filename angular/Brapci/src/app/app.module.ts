/* Core */
import { NgModule } from '@angular/core';

/* Module */
import { AppRoutingModule } from './app-routing.module';
import { OauthModule } from './001_auth/OauthModule';

/* Componente */
import { AppComponent } from './app.component';
/* Componente Header */
import { HeaderComponent } from './000_header/header/header.component';
import { FooterComponent } from './000_header/footer/footer.component';
import { NavbarComponent } from './000_header/navbar/navbar.component';

/* Componente Oauth */
import { UserPerfilComponent } from './001_auth/page/user-perfil/user-perfil.component';
import { UserChangePasswordComponent } from './001_auth/page/user-change-password/user-change-password.component';

/* Componente Header */
import { HomeComponent } from './002_main/home/home.component';
import { AboutComponent } from './002_main/home/about/about.component';
import { MainComponent } from './002_main/home/main/main.component';

/* Componente Header 100 */
import { BrapciHomeLivrosComponent } from './100_brapci_livros/page/brapci-home-livros/brapci-home-livros.component';
import { LivrosMainComponent } from './100_brapci_livros/livros-main/livros-main.component';
import { NavbarBooksComponent } from './100_brapci_livros/header/navbar-books/navbar-books.component';
import { LivrosBannersComponent } from './100_brapci_livros/page/livros-banners/livros-banners.component';
import { LivroVitrineComponent } from './100_brapci_livros/page/livro-vitrine/livro-vitrine.component';
import { LivroViewComponent } from './100_brapci_livros/page/livro-view/livro-view.component';

/* Componente Header 110 */
import { ViewAuthorityComponent } from './120_brapci_autoridades/page/view-authority/view-authority.component';
import { FindComponent } from './110_find/find/find.component';
import { BookServicesComponent } from './110_find/find/admin/book-services/book-services.component';
import { LivroSearchComponent } from './100_brapci_livros/page/livro-search/livro-search.component';
import { WelcomeComponent } from './002_main/page/welcome/welcome.component';
import { LivroSumarioComponent } from './100_brapci_livros/page/livro-sumario/livro-sumario.component';
import { LivroExemplaresComponent } from './100_brapci_livros/page/livro-exemplares/livro-exemplares.component';
import { Error404Component } from './000_header/error404/error404.component';
import { FindActionsComponent } from './110_find/find/admin/find-actions/find-actions.component';
import { BookAddComponent } from './110_find/find/admin/book-add/book-add.component';
import { BookPreparoListComponent } from './110_find/find/admin/book-preparo-list/book-preparo-list.component';
import { BookEditComponent } from './110_find/find/admin/book-edit/book-edit.component';
import { BookPreparoViewComponent } from './110_find/find/admin/book-preparo-view/book-preparo-view.component';
import { BlockAuthorsComponent } from './110_find/find/admin/book-edit/block-authors/block-authors.component';


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
    LivroViewComponent,
    LivroSearchComponent,
    WelcomeComponent,
    LivroSumarioComponent,
    LivroExemplaresComponent,
    Error404Component,
    FindActionsComponent,
    BookAddComponent,
    BookPreparoListComponent,
    BookEditComponent,
    BookPreparoViewComponent,
    BlockAuthorsComponent,
  ],

  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    OauthModule,
    RouterModule,
    FormsModule,
    ReactiveFormsModule,
    FormsModule,
    ReactiveFormsModule,
    MaterialModule,
  ],
  exports:[
    BookPreparoViewComponent, BookAddComponent, BlockAuthorsComponent,
  ],
  providers: [],
  bootstrap: [AppComponent],
})
export class AppModule { }
