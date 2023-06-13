import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SourcesService {
  private URL = 'https://cip.brapci.inf.br/api/source';
  constructor(private http: HttpClient) { }

  public getSources(): Observable<any> {
    return this.http.get(`${this.URL}`);
  }
}
