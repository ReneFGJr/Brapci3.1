import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-v-cite',
  templateUrl: './cite.component.html',
})
export class CiteComponent {
  @Input() public citacao: Array<any> | any;

  copiarTexto(conteudo: string) {
    navigator.clipboard
      .writeText(conteudo)
      .then(() => {
        alert('Texto copiado com sucesso!');
      })
      .catch((err) => {
        console.error('Erro ao copiar o texto: ', err);
      });
  }
}
