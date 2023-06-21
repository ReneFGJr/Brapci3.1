import { Component } from '@angular/core';

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
}
