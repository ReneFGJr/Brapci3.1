import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http'
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CollectionsService {
  private URL = 'https://cip.brapci.inf.br/api/source/collections';
  constructor(private http: HttpClient) { }

  public getCollections() : Observable<any>
    {
      return this.http.get(`${this.URL}`);
    }
}
