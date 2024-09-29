import { NgModule } from '@angular/core';
import { InfiniteScrollModule } from 'ngx-infinite-scroll';
import { CommonModule, HashLocationStrategy, LocationStrategy } from '@angular/common';

import { BannerComponent } from './page/banner/banner.component';
import { PainelComponent } from './page/painel/painel.component';
import { PublicationsComponent } from './page/painel/publications/publications.component';
import { SearchBrapciComponent } from './page/search-brapci/search-brapci.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { VComponent } from './page/v/v.component';
import { ThemeModule } from '../010_thema/theme.module';
import { ArticleComponent } from './page/v/article/article.component';
import { HeaderComponent } from './page/v/header/header.component';
import { BannerArticleComponent } from './page/banner/banner-article.component';
import { PdfComponent } from './page/pdf/pdf.component';
import { PaginationComponent } from './page/v/pagination/pagination.component';
import { JounalsComponent } from './page/jounals/jounals.component';
import { BrapciWelcomeComponent } from './page/welcome/welcome.component';
import { AboutComponent } from './page/about/about.component';
import { IndicadoresComponent } from './page/indicadores/indicadores.component';
import { DataVComponent } from './page/v/data/data.component';
import { PlumxComponent } from './page/v/metrics/plumx/plumx.component';
import { CiteComponent } from './page/v/cite.component';
import { BrapciProceedingComponent } from './page/v/proceeding/proceeding.component';
import { SubjectVComponent } from './page/v/subject/subject.component';
import { BasketComponent } from './page/basket/basket.component';
import { BasketedComponent } from './page/basketed/basketed.component';
import { DashboardComponent } from './page/dashboard/dashboard.component';
import { BookComponent } from './page/v/book/book.component';
import { DateComponent } from './page/v/date/date.component';
import { BannerBookComponent } from './page/banner/banner-book.component';
import { IssueBrapciComponent } from './page/v/issue/issue.component';
import { RDFinComponent } from './widget/rdfin/rdfin.component';
import { JournalComponent } from './page/v/journal/journal.component';
import { BrapciBannerComponent } from './widget/banner/banner.component';
import { PersonComponent } from './page/v/person/person.component';
import { GenericComponent } from './page/v/generic/generic.component';
import { FilestorageComponent } from './page/v/filestorage/filestorage.component';
import { BookchapterComponent } from './page/v/bookchapter/bookchapter.component';
import { BannerBenancibComponent } from './page/banner/banner-benancib/banner-benancib.component';
import { BenancibComponent } from './page/v/benancib/benancib.component';
import { EbbcComponent } from './page/v/ebbc/ebbc.component';
import { SearchBrapciAdvComponent } from './page/search-brapci-adv/search-brapci-adv.component';
import { SearchComponent } from './page/search/search.component';
import { SearchResultComponent } from './page/search-result/search-result.component';
import { TipsComponent } from './page/tips/tips.component';
import { IndexSubjectComponent } from './page/index-subject/index-subject.component';
import { IndexsComponent } from './page/indexs/indexs.component';
import { LoveItComponent } from './page/loveit/loveit.component';
import { EventComponent } from './widget/event/event.component';
import { NewsComponent } from './widget/news/news.component';
import { BasketedExportComponent } from './page/basketed-export/basketed-export.component';
import { ExportComponent } from './page/export/export.component';
import { AngularD3CloudModule } from 'angular-d3-cloud';
import { HighchartsChartModule } from 'highcharts-angular';
import { NetworkComponent } from './widget/network/network.component';
import { RemoveConceptComponent } from './widget/remove-concept/remove-concept.component';
import { Page404Component } from './widget/page404/page404.component';
import { NumberComponent } from './page/v/number/number.component';
import { EditConceptComponent } from './widget/edit-concept/edit-concept.component';
import { AiProcessComponent } from './widget/ai-process/ai-process.component';
import { TabsNavComponent } from './widget/tabs-nav/tabs-nav.component';
import { BannerEbbcComponent } from './page/banner/banner-ebbc/banner-ebbc.component';
import { Pg404Component } from './page/pg404/pg404.component';
import { PainelWorksComponent } from './widget/painel/works/works.component';
import { CoauthorsPainelComponent } from './widget/painel/coauthors/coauthors.component';
import { PainelFilterComponent } from './widget/painel/filter/filter.component';
import { AuthorsPainelComponent } from './widget/painel/authors/authors.component';
import { EditionsPainelComponent } from './widget/painel/editions/editions.component';
import { YearsPainelComponent } from './widget/painel/years/years.component';
import { CorporatebodyComponent } from './page/v/corporatebody/corporatebody.component';
import { BannerBrapciLivrosComponent } from './page/banner/banner-brapci-livros/banner-brapci-livros.component';
import { RecomendationComponent } from './page/recomendation/recomendation.component';
import { KeywordsComponent } from './widget/keywords/keywords.component';
import { SumaryComponent } from './widget/sumary/sumary.component';
import { ToolsSumaryComponent } from './widget/tools-sumary/tools-sumary.component';
import { IcrProducaoAnoComponent } from './widget/icr-producao-ano/icr-producao-ano.component';
import { IcrProducaoJournalAuthorComponent } from './widget/icr-producao-journal-author/icr-producao-journal-author.component';
import { TimelineJournalComponent } from './widget/timeline-journal/timeline-journal.component';
import { TimelineComponent } from './page/timeline/timeline.component';

@NgModule({
  declarations: [
    BannerComponent,
    PainelComponent,
    PublicationsComponent,
    SearchBrapciComponent,
    VComponent,
    ArticleComponent,
    HeaderComponent,
    BannerArticleComponent,
    PdfComponent,
    LoveItComponent,
    PaginationComponent,
    JounalsComponent,
    BrapciWelcomeComponent,
    AboutComponent,
    IndicadoresComponent,
    DataVComponent,
    PlumxComponent,
    CiteComponent,
    BrapciProceedingComponent,
    SubjectVComponent,
    BasketComponent,
    BasketedComponent,
    DashboardComponent,
    BookComponent,
    DateComponent,
    BannerBookComponent,
    IssueBrapciComponent,
    RDFinComponent,
    JournalComponent,
    BrapciBannerComponent,
    PersonComponent,
    GenericComponent,
    FilestorageComponent,
    BookchapterComponent,
    BannerBenancibComponent,
    BenancibComponent,
    EbbcComponent,
    SearchBrapciAdvComponent,
    SearchComponent,
    SearchResultComponent,
    TipsComponent,
    IndexSubjectComponent,
    IndexsComponent,
    EventComponent,
    NewsComponent,
    BasketedExportComponent,
    ExportComponent,
    NetworkComponent,
    RemoveConceptComponent,
    Page404Component,
    NumberComponent,
    EditConceptComponent,
    AiProcessComponent,
    TabsNavComponent,
    BannerEbbcComponent,
    Pg404Component,
    PainelWorksComponent,
    CoauthorsPainelComponent,
    PainelFilterComponent,
    AuthorsPainelComponent,
    EditionsPainelComponent,
    YearsPainelComponent,
    CorporatebodyComponent,
    BannerBrapciLivrosComponent,
    RecomendationComponent,
    KeywordsComponent,
    SumaryComponent,
    ToolsSumaryComponent,
    IcrProducaoAnoComponent,
    IcrProducaoJournalAuthorComponent,
    TimelineJournalComponent,
    TimelineComponent,
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule,
    InfiniteScrollModule,
    AngularD3CloudModule,
    HighchartsChartModule,
  ],
  exports: [
    BannerComponent,
    BrapciWelcomeComponent,
    PainelComponent,
    VComponent,
    BasketComponent,
    AuthorsPainelComponent,
    BannerBenancibComponent,
    BannerEbbcComponent,
  ],
  providers: [{ provide: LocationStrategy, useClass: HashLocationStrategy }],
})
export class CoreBrapciModule {}
