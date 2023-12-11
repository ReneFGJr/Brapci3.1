import { FileUploadService } from './../000_core/010_services/file-upload.service';
import { Component, EventEmitter, Output } from '@angular/core';

@Component({
  selector: 'app-upload-file',
  templateUrl: './upload-file.component.html',
})
export class UploadFileComponent {
  constructor(private fileUploadService: FileUploadService) {}

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

  // OnClick of button Upload
  onUpload() {
    this.loading = !this.loading;
    this.fileUploadService.upload(this.file).subscribe((event: any) => {
      console.log("======FILE=1======")
      this.newItemEvent.emit(event);
      console.log('======FILE=2======');
    });
  }
}
