import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeChatComponent } from './page/home-chat/home-chat.component';

const routes: Routes = [{ path: '', component: HomeChatComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ChatRoutingModule { }
