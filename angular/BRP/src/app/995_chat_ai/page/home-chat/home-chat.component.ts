import { Component } from '@angular/core';
import { ChatServiceService } from '../../service/chat-service.service';

@Component({
  selector: 'app-home-chat',
  templateUrl: './home-chat.component.html',
  styleUrls: ['./home-chat.component.scss'],
})
export class HomeChatComponent {
  messages: Array<{ user: string; text: string }> = [];
  messageText: string = '';

  constructor(private chatService: ChatServiceService) {}

  sendMessage() {
    if (this.messageText.trim() === '') return;

    this.messages.push({ user: 'You', text: this.messageText });

    this.chatService.sendMessage(this.messageText).subscribe((response) => {
      this.messages.push({
        user: 'Ollama',
        text: response.response || 'Erro na resposta',
      });
    });

    this.messageText = '';
  }
}
