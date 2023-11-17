import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from 'src/app/001_auth/service/user.service';

@Component({
  selector: 'app-login-icone',
  templateUrl: './login-icone.component.html',
  styleUrls: ['./login-icone.component.scss'],
})
export class LoginIconeComponent {
  @Input() public user: Array<any> | any;
//  public user:Array<any> | any

  constructor(private userService: UserService, private router: Router) {}

  login() {
    this.router.navigate(['/social/signin']);
  }

  perfil() {
    this.router.navigate(['/social/perfil']);
  }
}
