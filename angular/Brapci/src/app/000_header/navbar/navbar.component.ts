import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent {
  constructor(private router: Router) {}
  public logo ='/assets/img/logo_brapci.png';

  link_livros()
    {
      window.location.href ='/books';
    }
}
