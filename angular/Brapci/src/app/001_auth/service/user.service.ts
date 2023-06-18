import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { UIuser } from '../interface/UIusers';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  constructor(
            private HttpClient: HttpClient,
            private Router: Router) { }

  signin()
    {
        alert("HELLO");
    }

  logout()
    {

    }

  signup()
    {

    }
}
