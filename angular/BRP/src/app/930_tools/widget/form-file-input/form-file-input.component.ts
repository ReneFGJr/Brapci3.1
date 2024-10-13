import { HttpClient, HttpEventType } from '@angular/common/http';
import { Component, Input } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { Subscription } from 'rxjs';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-form-file-input',
  templateUrl: './form-file-input.component.html',
  styleUrls: ['./form-file-input.component.scss'],
})
export class FormFileInputComponent {
  @Input() public action: string = 'none'
  selectedFile: File | null = null;
  uploadProgress: number = 0;

  /*************** Inport */
  public propriety: string = 'hasAuthor';
  public class: string = 'Article';
  public xClass: string = '';
  public ID: string = '0';
  public text: string = '';
  public type: string = 'temp';

  /********************* BTN */
  public btn1: boolean = true;
  public btn2: boolean = true;
  public btn3: boolean = true;

  /******************** File */
  status: 'initial' | 'uploading' | 'success' | 'fail' = 'initial'; // Variable to store file status
  file: File | null = null; // Variable to store file

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private http: HttpClient
  ) {}

  // Função para capturar o arquivo selecionado
  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];
    alert(this.selectedFile);
  }

  onUpload() {
    if (this.file) {
      const formData = new FormData();

      console.log(this.propriety);
      console.log('+++' + this.type);
      let url = this.brapciService.url + 'sendfile/' + this.action;
      //let url = 'http://brp/api/' + 'upload/' + this.type + '/' + this.ID
      console.log(url);

      formData.append('file', this.file, this.file.name);
      formData.append('property', this.propriety);
      const upload$ = this.http.post(url, formData);
      this.status = 'uploading';

      upload$.subscribe({
        next: (x) => {
          console.log(x);
          this.status = 'success';
        },
        error: (error: any) => {
          this.status = 'fail';
          return error;
        },
      });
    }
  }

  // On file Select
  onChange(event: any) {
    const file: File = event.target.files[0];

    if (file) {
      this.status = 'initial';
      this.file = file;
    }
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
        .post('https://cip.brapci.inf.br/api/sendfile', formData, {
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
