import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ToolsMainComponent } from './main/tools-main/tools-main.component';
import { ToolsIconsComponent } from './page/main/tools-icons/tools-icons.component';

const routes: Routes = [
  {
    path: '',
    component: ToolsMainComponent,
    children: [
      { path: '', component: ToolsIconsComponent },
      { path: 'v/:id', component: ToolsIconsComponent },
      { path: 'viewdata/:id', component: ToolsIconsComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BrapciToolsRoutingModule { }
