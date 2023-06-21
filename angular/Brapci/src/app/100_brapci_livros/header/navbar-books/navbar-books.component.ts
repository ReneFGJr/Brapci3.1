import { Component } from '@angular/core';
import { Router } from "@angular/router"

@Component({
  selector: 'app-navbar-books',
  templateUrl: './navbar-books.component.html',
  styleUrls: ['./navbar-books.component.css']
})
export class NavbarBooksComponent {
  public book_home: string = 'Home';
  public book_index: string = 'Índices';
  public book_subject: string = 'Assuntos';
  public book_admin: string = 'Administrador';
  public book_about: string = 'Sobre';
  public book_contact: string = 'Contato';

  constructor(private router: Router) { }

  home()
    {
        this.router.navigate(['/books'])
    }

  indexs() {
    this.router.navigate(['/books/index'])
  }

  subjects() {
    this.router.navigate(['/books/subjects'])
  }

  admin() {
    window.location.href = '/find/admin';
  }

}
