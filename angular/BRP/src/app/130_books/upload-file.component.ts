import { HttpClient } from '@angular/common/http';
import { FileUploadService } from './../000_core/010_services/file-upload.service';
import { Component, EventEmitter, Output } from '@angular/core';

@Component({
  selector: 'app-upload-file',
  templateUrl: './upload-file.component.html',
})
export class UploadFileComponent {
  selectedFile: File | null = null;

  constructor(
    private fileUploadService: FileUploadService,
    private http: HttpClient
  ) {}

  @Output() newItemEvent = new EventEmitter<string>();

  // Variable to store shortLink from api response
  shortLink: string = '';
  loading: boolean = false; // Flag variable
  public file: File | any; // Variable to store file

  ngOnInit(): void {}

  // On file Select
  onChange(event: any) {
    this.file = event.target.files[0];
  }

  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];
  }

  // OnClick of button Upload
  onUpload() {
    this.loading = !this.loading;
    this.fileUploadService.upload(this.file).subscribe((event: any) => {
      console.log('======FILE=1======');
      this.newItemEvent.emit(event);
      console.log(event.message);
      console.log(event)
      console.log('======FILE=2======');
    });
  }

  onUploadNew() {
    if (this.selectedFile) {
      const formData = new FormData();
      formData.append('file', this.selectedFile, this.selectedFile.name);

      this.http.post('YOUR_API_ENDPOINT', formData).subscribe((response) => {
        console.log(response);
      });
    } else {
      alert('No file selected');
    }
  }
}
