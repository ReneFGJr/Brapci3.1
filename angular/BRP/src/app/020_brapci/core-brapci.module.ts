import { NgModule } from '@angular/core';
import { InfiniteScrollModule } from 'ngx-infinite-scroll';
import { CommonModule, HashLocationStrategy, LocationStrategy } from '@angular/common';

import { BannerComponent } from './page/banner/banner.component';
import { PainelComponent } from './page/painel/painel.component';
import { PublicationsComponent } from './page/painel/publications/publications.component';
import { AuthorsComponent } from './page/painel/authors/authors.component';
import { KeywordsComponent } from './page/painel/keywords/keywords.component';
import { SearchBrapciComponent } from './page/search-brapci/search-brapci.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { VComponent } from './page/v/v.component';
import { ThemeModule } from '../010_thema/theme.module';
import { ArticleComponent } from './page/v/article/article.component';
import { HeaderComponent } from './page/v/header/header.component';
import { BannerArticleComponent } from './page/banner/banner-article.component';
import { PdfComponent } from './v/component/pdf/pdf.component';
import { PaginationComponent } from './page/v/pagination/pagination.component';
import { JounalsComponent } from './page/jounals/jounals.component';
import { BrapciWelcomeComponent } from './page/welcome/welcome.component';
import { AboutComponent } from './page/about/about.component';
import { IndicadoresComponent } from './page/indicadores/indicadores.component';
import { DataComponent } from './page/v/data/data.component';
import { PlumxComponent } from './page/v/metrics/plumx/plumx.component';


@NgModule({
  declarations: [
    BannerComponent,
    PainelComponent,
    PublicationsComponent,
    AuthorsComponent,
    KeywordsComponent,
    SearchBrapciComponent,
    VComponent,
    ArticleComponent,
    HeaderComponent,
    BannerArticleComponent,
    PdfComponent,
    PaginationComponent,
    JounalsComponent,
    BrapciWelcomeComponent,
    AboutComponent,
    IndicadoresComponent,
    DataComponent,
    PlumxComponent,
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule,
    InfiniteScrollModule
  ],
  exports:[
    BannerComponent,
    BrapciWelcomeComponent,
    PainelComponent,
    VComponent
  ],
  providers: [{ provide: LocationStrategy, useClass: HashLocationStrategy }],
})
export class CoreBrapciModule { }
