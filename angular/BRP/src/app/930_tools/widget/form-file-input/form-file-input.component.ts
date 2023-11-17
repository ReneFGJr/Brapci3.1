import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';

@Component({
  selector: 'app-form-file-input',
  templateUrl: './form-file-input.component.html',
  styleUrls: ['./form-file-input.component.scss'],
})
export class FormFileInputComponent {
  public fileName: string = '';

  constructor(private http: HttpClient) {}

  onFileSelected() {

  }
}
