import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ToolsMainComponent } from './main/tools-main/tools-main.component';
import { ToolsIconsComponent } from './page/main/tools-icons/tools-icons.component';
import { Txt4netComponent } from './tools/txt4net/txt4net.component';
import { AmostraComponent } from './page/amostra/amostra.component';
import { PriceComponent } from './page/amostra/price.component';
import { BradfordComponent } from './page/amostra/bradford.component';
import { LotkaComponent } from './page/amostra/lotka.component';
import { QrcodeComponent } from './widget/qrcode/qrcode.component';
import { SumaryToolsComponent } from './tools/sumary-tools/sumary-tools.component';
import { Ris4marcComponent } from './widget/marc21/ris4marc/ris4marc.component';

const routes: Routes = [
  {
    path: '',
    component: ToolsMainComponent,
    children: [
      { path: '', component: ToolsIconsComponent },
      { path: 'v/:id', component: ToolsIconsComponent },
      { path: 'viewdata/:id', component: ToolsIconsComponent },
      { path: 'txt4net', component: Txt4netComponent },
      { path: 'txt4matrix', component: ToolsIconsComponent },
      { path: 'net4gephi', component: ToolsIconsComponent },
      { path: 'txt4char', component: ToolsIconsComponent },
      { path: 'ris4marc', component: Ris4marcComponent },
      { path: 'txtChange', component: ToolsIconsComponent },
      { path: 'amostra', component: AmostraComponent },
      { path: 'price', component: PriceComponent },
      { path: 'bradford', component: BradfordComponent },
      { path: 'lotka', component: LotkaComponent },
      { path: 'qrcode', component: QrcodeComponent },
      { path: 'summary/:id', component: SumaryToolsComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BrapciToolsRoutingModule { }
