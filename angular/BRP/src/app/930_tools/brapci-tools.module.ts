import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BrapciToolsRoutingModule } from './brapci-tools-routing.module';
import { ToolsMainComponent } from './main/tools-main/tools-main.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ThemeModule } from '../010_thema/theme.module';
import { ToolsIconsComponent } from './page/main/tools-icons/tools-icons.component';
import { Txt4netComponent } from './tools/txt4net/txt4net.component';
import { Txt4matrixComponent } from './tools/txt4matrix/txt4matrix.component';
import { Txt4charComponent } from './tools/txt4char/txt4char.component';
import { TxtChangeComponent } from './tools/txt-change/txt-change.component';
import { Txt4gephiComponent } from './tools/txt4gephi/txt4gephi.component';
import { FormFileInputComponent } from './widget/form-file-input/form-file-input.component';
import { TextFormComponent } from './widget/text-form/text-form.component';
import { BannerComponent } from './widget/banner/banner.component';
import { AmostraComponent } from './page/amostra/amostra.component';
import { PriceComponent } from './page/amostra/price.component';
import { BradfordComponent } from './page/amostra/bradford.component';
import { LotkaComponent } from './page/amostra/lotka.component';
import { MenuRightSideComponent } from './page/amostra/menu-right-side/menu-right-side.component';
import { QRCodeModule } from 'angularx-qrcode';
import { QrcodeComponent } from './widget/qrcode/qrcode.component';
import { SumaryToolsComponent } from './tools/sumary-tools/sumary-tools.component';
import { BrandToolsComponent } from './widget/brand-tools/brand-tools.component';
import { Ris4marcComponent } from './widget/marc21/ris4marc/ris4marc.component';
import { MonitorComponent } from './widget/monitor/monitor.component';
import { ProcessingComponent } from './widget/processing/processing.component';
import { ProcessingWorkflowComponent } from './widget/processing-workflow/processing-workflow.component';
import { Txt4unitComponent } from './tools/txt4unit/txt4unit.component';
import { Txt4unit2Component } from './tools/txt4unit2/txt4unit2.component';
import { AiLlmComponent } from './page/ai-llm/ai-llm.component';

@NgModule({
  declarations: [
    ToolsMainComponent,
    ToolsIconsComponent,
    Txt4netComponent,
    Txt4matrixComponent,
    Txt4charComponent,
    TxtChangeComponent,
    Txt4gephiComponent,
    FormFileInputComponent,
    TextFormComponent,
    BannerComponent,
    AmostraComponent,
    PriceComponent,
    BradfordComponent,
    LotkaComponent,
    MenuRightSideComponent,
    QrcodeComponent,
    SumaryToolsComponent,
    BrandToolsComponent,
    Ris4marcComponent,
    MonitorComponent,
    ProcessingComponent,
    ProcessingWorkflowComponent,
    Txt4unitComponent,
    Txt4unit2Component,
    AiLlmComponent,
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule,
    BrapciToolsRoutingModule,
    QRCodeModule,
  ],
})
export class BrapciToolsModule {}
