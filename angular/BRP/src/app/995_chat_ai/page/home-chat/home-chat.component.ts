import { Component } from '@angular/core';
import { OllamaService } from 'src/app/000_core/010_services/ollama.service';

interface Message {
  text: string;
  sender: 'user' | 'ollama';
}

@Component({
  selector: 'app-home-chat',
  templateUrl: './home-chat.component.html',
  styleUrls: ['./home-chat.component.scss'],
})
export class HomeChatComponent {
  messages: Message[] = [];
  userInput: string = '';
  isLoading: boolean = false;
  errorMessage: string = '';

  constructor(private ollamaService: OllamaService) {}

  ngOnInit(): void {
    // Inicializar com uma mensagem de boas-vindas, se desejar
    this.messages.push({
      text: 'Olá! Como posso ajudar você hoje?',
      sender: 'ollama',
    });
  }

  sendMessage(): void {
    const messageText = this.userInput.trim();
    if (!messageText) {
      return;
    }

    // Adicionar a mensagem do usuário à lista
    this.messages.push({ text: messageText, sender: 'user' });
    this.userInput = '';
    this.isLoading = true;
    this.errorMessage = '';

    // Enviar a mensagem para a API do Ollama
    this.ollamaService.sendMessage(messageText).subscribe({
      next: (response) => {
        // Supondo que a resposta contenha a mensagem em 'response.reply'
        const reply = response.reply || 'Desculpe, não entendi.';
        this.messages.push({ text: reply, sender: 'ollama' });
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erro ao comunicar com Ollama:', error);
        this.errorMessage =
          'Ocorreu um erro ao enviar a mensagem. Por favor, tente novamente.';
        this.isLoading = false;
      },
    });
  }

  handleKeyPress(event: KeyboardEvent): void {
    if (event.key === 'Enter') {
      this.sendMessage();
    }
  }
}
