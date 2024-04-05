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
      { path: 'txtChange', component: ToolsIconsComponent },
      { path: 'amostra', component: AmostraComponent },
      { path: 'price', component: PriceComponent },
      { path: 'bradford', component: BradfordComponent },
      { path: 'lotka', component: LotkaComponent },
      { path: 'qrcode', component: QrcodeComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BrapciToolsRoutingModule { }
