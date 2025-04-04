import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ChatRoutingModule } from './chat-routing.module';
import { HomeChatComponent } from './page/home-chat/home-chat.component';
import { FormsModule } from '@angular/forms';


@NgModule({
  declarations: [HomeChatComponent],
  imports: [CommonModule, ChatRoutingModule, FormsModule],
})
export class ChatModule {}
