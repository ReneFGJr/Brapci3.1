import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs';
import { environment } from 'environments/environment';

@Injectable({
  providedIn: 'root'
})
export class SourcesService {
  private URL = `${environment.HTTP}/source`;

  constructor(private http: HttpClient) { }

  public getSources(): Observable<any> {
    return this.http.get(`${this.URL}`);
  }
}
