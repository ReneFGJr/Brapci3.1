import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EventsRoutingModule } from './events-routing.module';
import { IndexEventComponent } from './page/index/index.component';
import { ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ThemeModule } from '../010_thema/theme.module';
import { InfiniteScrollModule } from 'ngx-infinite-scroll';


@NgModule({
  declarations: [IndexEventComponent],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    ThemeModule,
    InfiniteScrollModule,
  ],
})
export class EventsModule {}
