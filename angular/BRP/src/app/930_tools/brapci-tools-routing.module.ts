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
import { MonitorComponent } from './widget/monitor/monitor.component';
import { Txt4unitComponent } from './tools/txt4unit/txt4unit.component';
import { Txt4unit2Component } from './tools/txt4unit2/txt4unit2.component';
import { AiLlmComponent } from './page/ai-llm/ai-llm.component';
import { Txt4matrixComponent } from './tools/txt4matrix/txt4matrix.component';

const routes: Routes = [
  {
    path: '',
    component: ToolsMainComponent,
    children: [
      { path: '', component: ToolsIconsComponent },
      { path: 'v/:id', component: ToolsIconsComponent },
      { path: 'ai/:id', component: AiLlmComponent },
      { path: 'viewdata/:id', component: ToolsIconsComponent },
      { path: 'txt4net', component: Txt4netComponent },
      { path: 'txt4matrix', component: Txt4matrixComponent },
      { path: 'txt4unit', component: Txt4unitComponent },
      { path: 'txt4unit2', component: Txt4unit2Component },
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
      { path: 'monitor', component: MonitorComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BrapciToolsRoutingModule { }
