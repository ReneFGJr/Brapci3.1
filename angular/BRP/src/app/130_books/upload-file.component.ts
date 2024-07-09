import { HttpClient, HttpEventType, HttpResponse } from '@angular/common/http';
import { FileUploadService } from './../000_core/010_services/file-upload.service';
import { Component, EventEmitter, Output } from '@angular/core';

@Component({
  selector: 'app-upload-file',
  templateUrl: './upload-file.component.html',
})
export class UploadFileComponent {
  selectedFile: File | null = null;
  uploadProgress: number | null = null;
  uploadedFileUrl: string | null = null;
  uploadedFileName: string | null = null;

  constructor(private http: HttpClient) {}

  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];
  }

  onUpload() {
    if (this.selectedFile) {
      const formData = new FormData();
      formData.append('file', this.selectedFile, this.selectedFile.name);

      this.http
        .post('https://cip.brapci.inf.br/api/book/submit', formData, {
          reportProgress: true,
          observe: 'events',
        })
        .subscribe((event) => {
          if (event.type === HttpEventType.UploadProgress) {
            this.uploadProgress = Math.round(
              (100 * event.loaded) / (event.total ?? 1)
            );
          } else if (event instanceof HttpResponse) {
            this.uploadProgress = null;
            this.uploadedFileUrl = 'https://cip.brapci.inf.br/api/book/submit'; // Adjust based on your response
            this.uploadedFileName = this.selectedFile?.name ?? '';
          }
        });
    } else {
      alert('No file selected');
    }
  }
  @Output() newItemEvent = new EventEmitter<string>();
}
