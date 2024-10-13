import { Component } from '@angular/core';

@Component({
  selector: 'app-wait-processing',
  templateUrl: './processing.component.html',
  styleUrls: ['./processing.component.scss'],
})
export class ProcessingComponent {
  isProcessing: boolean = false; // Controle para mostrar/ocultar a mensagem de processamento
  ngOnInit()
    {
      this.isProcessing = true
    }
  // Função que inicia o processamento
  startProcessing() {
    this.isProcessing = true;
    // Simula o término do processamento após 5 segundos
    setTimeout(() => {
      this.isProcessing = false;
    }, 5000); // Altere conforme necessário
  }
}
