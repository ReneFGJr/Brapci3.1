import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ToolsMainComponent } from './main/tools-main/tools-main.component';
import { ToolsIconsComponent } from './page/main/tools-icons/tools-icons.component';
import { Txt4netComponent } from './tools/txt4net/txt4net.component';

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
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BrapciToolsRoutingModule { }
