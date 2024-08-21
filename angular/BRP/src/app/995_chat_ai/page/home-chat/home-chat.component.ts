import { Component } from '@angular/core';
import { ChatServiceService } from '../../service/chat-service.service';
import { HttpHeaders } from '@angular/common/http';


@Component({
  selector: 'app-home-chat',
  templateUrl: './home-chat.component.html',
  styleUrls: ['./home-chat.component.scss'],
})
export class HomeChatComponent {
  messages: { user: string; text: string }[] = [];
  userMessage: string = '';

  constructor(private chatService: ChatServiceService) {}

  async sendMessage() {
    if (this.userMessage.trim()) {
      // Adiciona a mensagem do usuário ao chat
      this.messages.push({ user: 'Você', text: this.userMessage });

      try {
        // Envia a mensagem para a API e espera a resposta síncrona
        const response = await this.chatService.sendMessageSync(
          this.userMessage
        );

        console.log(response)

        // Adiciona a resposta completa da API ao chat
        this.messages.push({ user: 'Ollama', text: response });
      } catch (error) {
        // Trata erros na resposta da API
        this.messages.push({
          user: 'Ollama',
          text: 'Erro ao processar a mensagem.',
        });
      }

      // Limpa o campo de entrada do usuário
      this.userMessage = '';
    }
  }
}
