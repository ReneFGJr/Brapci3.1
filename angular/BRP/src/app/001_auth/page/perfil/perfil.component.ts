import { Component } from '@angular/core';
import { UserService } from '../../service/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-perfil',
  templateUrl: './perfil.component.html',
  styleUrls: ['./perfil.component.scss'],
})
export class PerfilComponent {
  constructor(private userService: UserService, private router: Router) {}

  public user: Array<any> | any;

  ngOnInit() {
    this.user = this.userService.getUser();
  }

  logout()
    {
      this.userService.logout();
      this.router.navigate(['/']);
    }
}
