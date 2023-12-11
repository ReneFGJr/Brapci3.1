import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class FileUploadService {
  [x: string]: any;
  baseApiUrl = 'https://cip.brapci.inf.br/api/brapci/upload';
  constructor(private HttpClient: HttpClient) {}

  // Returns an observable
  upload(file: any): Observable<any> {
    // Create form data
    const formData = new FormData();

    // Store form name as "file" with file data
    formData.append('file', file, file.name);
    return this.HttpClient.post<Array<any>>(this.baseApiUrl, formData).pipe(
      (res) => res,
      (error) => error
    );
  }
}
