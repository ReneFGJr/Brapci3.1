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



@NgModule({
  declarations: [
    BannerComponent,
    PainelComponent,
    PublicationsComponent,
    AuthorsComponent,
    KeywordsComponent,
    SearchBrapciComponent,
    SearchBrapciResultsComponent,
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule
  ],
  exports:[
    BannerComponent,
    PainelComponent
  ]
})
export class CoreBrapciModule { }
