import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BannerComponent } from './page/banner/banner.component';
import { PainelComponent } from './page/painel/painel.component';
import { PublicationsComponent } from './page/painel/publications/publications.component';
import { AuthorsComponent } from './page/painel/authors/authors.component';
import { KeywordsComponent } from './page/painel/keywords/keywords.component';
import { SearchBrapciComponent } from './page/search-brapci/search-brapci.component';
import { ReactiveFormsModule } from '@angular/forms';
import { SearchBrapciResultsComponent } from './page/search-brapci-results/search-brapci-results.component';
import { RouterModule } from '@angular/router';
import { VComponent } from './page/v/v.component';
import { ThemeModule } from '../010_thema/theme.module';
import { ArticleComponent } from './page/v/article/article.component';
import { HeaderComponent } from './page/v/header/header.component';
import { BannerArticleComponent } from './page/banner/banner-article.component';
import { PdfComponent } from './v/component/pdf/pdf.component';

@NgModule({
  declarations: [
    BannerComponent,
    PainelComponent,
    PublicationsComponent,
    AuthorsComponent,
    KeywordsComponent,
    SearchBrapciComponent,
    SearchBrapciResultsComponent,
    VComponent,
    ArticleComponent,
    HeaderComponent,
    BannerArticleComponent,
    PdfComponent,
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule
  ],
  exports:[
    BannerComponent,
    PainelComponent,
    VComponent
  ]
})
export class CoreBrapciModule { }
