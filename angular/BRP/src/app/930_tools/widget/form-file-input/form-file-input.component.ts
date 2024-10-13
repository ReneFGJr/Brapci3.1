import { HttpClient, HttpEventType } from '@angular/common/http';
import { Component } from '@angular/core';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-form-file-input',
  templateUrl: './form-file-input.component.html',
  styleUrls: ['./form-file-input.component.scss'],
})
export class FormFileInputComponent {
  selectedFile: File | null = null;
  uploadProgress: number = 0;

  constructor(private http: HttpClient) {}

  // Função para capturar o arquivo selecionado
  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];
  }

  // Função para fazer o upload com percentagem e variável de controle
  onSubmit() {
    if (this.selectedFile) {
      const formData = new FormData();
      formData.append('file', this.selectedFile);

      // Adicionando variável de controle, ex: "uploadType"
      const uploadType = 'backup'; // Exemplo de valor
      formData.append('uploadType', uploadType);

      this.http
        .post('https://cip.brapci.inf.br/api/upload/', formData, {
          reportProgress: true,
          observe: 'events',
        })
        .subscribe(
          (event) => {
            if (event.type === HttpEventType.UploadProgress) {
              if (event.total) {
                this.uploadProgress = Math.round(
                  100 * (event.loaded / event.total)
                );
              }
            } else if (event.type === HttpEventType.Response) {
              console.log('Upload concluído:', event.body);
            }
          },
          (error) => {
            console.error('Erro no upload:', error);
          }
        );
    }
  }
}
