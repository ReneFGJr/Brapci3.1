import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainComponent } from './000_main/main/main.component';
import { VComponent } from '../020_brapci/page/v/v.component';

const routes: Routes = [
  { path: '', component: MainComponent },
  { path: 'v/:id', component: VComponent },
  { path: 'v', component: VComponent },
  { path: 'authority', loadChildren: () => import('../100_authority/authotity-core.module').then(m => m.Core100Module) } ,
  { path: 'social', loadChildren: () => import('../001_auth/oauth.module').then(m => m.OauthModule) },
  { path: 'journals', loadChildren: () => import('../110_journals/journals.module').then(m => m.JournalsModule) },
  { path: 'proceedings', loadChildren: () => import('../120_proceddings/proceddings.module').then(m => m.ProceddingsModule) },
  { path: 'sources', loadChildren: () => import('../040_source/source.module').then(m => m.SourceModule) },
  { path: 'oai', loadChildren: () => import('../030_oai/oaipmh.module').then(m => m.OaipmhModule) },
  { path: 'books', loadChildren: () => import('../130_books/books.module').then(m => m.BooksModule)}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CoreRoutingModule {
  constructor() {}

  ngInitOn()
    {
      console.log('init')
    }

}
