import { HttpClient, HttpEventType } from '@angular/common/http';
import { Component, Input, Output, EventEmitter } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { Subscription } from 'rxjs';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-form-file-input',
  templateUrl: './form-file-input.component.html',
  styleUrls: ['./form-file-input.component.scss'],
})
export class FormFileInputComponent {
  @Input() public action: string = 'none';
  @Output() dataset = new EventEmitter<Array<any>>();
  selectedFile: File | null = null;
  uploadProgress: number = 0;

  /*************** Inport */
  public propriety: string = 'hasAuthor';
  public class: string = 'Article';
  public xClass: string = '';
  public ID: string = '0';
  public text: string = '';
  public type: string = 'temp';
  public filename: string = '';

  /********************* BTN */
  public btn1: boolean = true;
  public btn2: boolean = true;
  public btn3: boolean = true;

  /******************** File */
  status: 'initial' | 'ready' | 'uploading' | 'success' | 'fail' = 'initial'; // Variable to store file status
  file: File | null = null; // Variable to store file

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private http: HttpClient
  ) {}

  // Função para capturar o arquivo selecionado
  onFileSelected(event: any) {
    this.file = event.target.files[0]; // Corrigido para usar this.file
    this.status = 'ready';
  }

  // Função para fazer o upload sem exibir progresso
  onUpload() {
    if (this.file) {
      const formData = new FormData();
      let url = this.brapciService.url + 'sendfile/' + this.action;

      formData.append('file', this.file, this.file.name);
      formData.append('property', this.propriety);

      const upload$ = this.http.post(url, formData);
      this.status = 'uploading';

      upload$.subscribe({
        next: (x: any) => {
          // Usa `any` para ignorar o erro de tipo
          if (x.fileO) {
            this.filename = x.fileO; // Agora atribui o valor de fileO a filename
          }
          this.status = 'success';
          this.dataset.emit(x);
        },
        error: (error: any) => {
          this.status = 'fail';
          console.error('Erro no upload:', error);
        },
      });
    }
  }

  // Função para capturar arquivo e mostrar progresso de upload
  onSubmit() {
    if (this.file) {
      const formData = new FormData();
      formData.append('file', this.file);

      // Adicionando variável de controle, ex: "uploadType"
      const uploadType = 'backup'; // Exemplo de valor
      formData.append('uploadType', uploadType);

      this.http
        .post('https://cip.brapci.inf.br/api/sendfile', formData, {
          reportProgress: true,
          observe: 'events',
        })
        .subscribe(
          (event: any) => {
            if (event.type === HttpEventType.UploadProgress) {
              if (event.total) {
                this.uploadProgress = Math.round(
                  100 * (event.loaded / event.total)
                );
              }
            } else if (event.type === HttpEventType.Response) {
              console.log('Upload concluído:', event.body);
              this.status = 'success';
            }
          },
          (error) => {
            this.status = 'fail';
            console.error('Erro no upload:', error);
          }
        );
    }
  }
}
