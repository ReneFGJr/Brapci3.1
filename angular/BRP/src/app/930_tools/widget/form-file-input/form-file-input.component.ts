import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-form-file-input',
  templateUrl: './form-file-input.component.html',
  styleUrls: ['./form-file-input.component.scss'],
})
export class FormFileInputComponent {
  public fileName: string = '';

  constructor(private http: HttpClient) {}
  public action: string = 'txt4net';
  public uploadProgress: number = 0;
  public uploadSub?: Subscription;
  public uploadSubZ?: Subscription;

  onFileSelected(event: Array<any> | any) {
    console.log(event);
    const file: File = event.target.files[0];
    if (file) {
      console.log('+++++++++++++++++++++++++++++++++++++');
      console.log(file);
      this.fileName = file.name;
      const formData = new FormData();
      formData.append('file', file);
      const upload$ = this.http.post(
        'https://cip.brapci.inf.br/api/tools/' + this.action + '/',
        formData
      );
      upload$.subscribe();
    }
  }
  reset() {
    this.uploadProgress = 0;
    this.uploadSub = this.uploadSubZ;
  }

  cancelUpload() {
    //this.uploadSub.unsubscribe();
    this.reset();
  }
}
