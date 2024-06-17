import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { MainComponent } from './000_main/main/main.component';
import { VComponent } from '../020_brapci/page/v/v.component';
import { BasketedComponent } from '../020_brapci/page/basketed/basketed.component';
import { DashboardComponent } from '../020_brapci/page/dashboard/dashboard.component';
import { AboutComponent } from '../020_brapci/page/about/about.component';
import { SearchBrapciAdvComponent } from '../020_brapci/page/search-brapci-adv/search-brapci-adv.component';
import { IndexsComponent } from '../020_brapci/page/indexs/indexs.component';
import { ExportComponent } from '../020_brapci/page/export/export.component';
import { Pg404Component } from '../020_brapci/page/pg404/pg404.component';
import { PqModule } from '../160_PQ/pq.module';
import { PainelComponent } from '../020_brapci/page/painel/painel.component';

const routes: Routes = [
  { path: '', component: MainComponent },
  { path: 'search-adv', component: SearchBrapciAdvComponent },
  { path: 'about', component: AboutComponent },
  { path: 'about/:id', component: AboutComponent },
  { path: 'indexs', component: IndexsComponent },
  { path: 'indexs/:type/:id/:lang', component: IndexsComponent },
  { path: 'indexs/:type/:id', component: IndexsComponent },
  { path: 'v/:id', component: VComponent },
  { path: 'v', component: VComponent },
  { path: 'basket/selected', component: BasketedComponent },
  { path: 'basket/export/:id', component: ExportComponent },
  { path: 'dashboard', component: DashboardComponent },
  { path: '404', component: Pg404Component },
  { path: 'painel/:id', component: PainelComponent },
  {
    path: 'authority',
    loadChildren: () =>
      import('../100_authority/authotity-core.module').then(
        (m) => m.Core100Module
      ),
  },
  {
    path: 'dataset',
    loadChildren: () =>
      import('../940_dataset/source.module').then((m) => m.SourceModule),
  },
  {
    path: 'social',
    loadChildren: () =>
      import('../001_auth/oauth.module').then((m) => m.OauthModule),
  },
  {
    path: 'bots',
    loadChildren: () => import('../bots/bots.module').then((m) => m.BotsModule),
  },
  {
    path: 'journals',
    loadChildren: () =>
      import('../110_journals/journals.module').then((m) => m.JournalsModule),
  },
  {
    path: 'proceedings',
    loadChildren: () =>
      import('../120_proceddings/proceddings.module').then(
        (m) => m.ProceddingsModule
      ),
  },
  {
    path: 'pq',
    loadChildren: () => import('../160_PQ/pq.module').then((m) => m.PqModule),
  },
  {
    path: 'sources',
    loadChildren: () =>
      import('../040_source/source.module').then((m) => m.SourceModule),
  },
  {
    path: 'oai',
    loadChildren: () =>
      import('../030_oai/oaipmh.module').then((m) => m.OaipmhModule),
  },
  {
    path: 'books',
    loadChildren: () =>
      import('../130_books/books.module').then((m) => m.BooksModule),
  },
  {
    path: 'kanban',
    loadChildren: () =>
      import('../900_kanban/kanban.module').then((m) => m.KanbanModule),
  },
  {
    path: 'tools',
    loadChildren: () =>
      import('../930_tools/brapci-tools.module').then(
        (m) => m.BrapciToolsModule
      ),
  },
  {
    path: 'rdf',
    loadChildren: () =>
      import('../920_RDF/rdf.module').then((m) => m.RdfModule),
  },
  {
    path: 'popup',
    loadChildren: () =>
      import('../990_popup/popup.module').then((m) => m.PopupModule),
  },
  {
    path: 'event',
    loadChildren: () =>
      import('../150_events/events.module').then((m) => m.EventsModule),
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CoreRoutingModule {
  constructor() {}
}
