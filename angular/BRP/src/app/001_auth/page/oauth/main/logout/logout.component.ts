import { Component } from '@angular/core';
import { UserService } from '../../../../service/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-oauth-logout',
  template: 'LOGOUT'
})
export class LogoutComponent {

  constructor(
    private userService: UserService,
    private router: Router
  ) { }

  ngOnInit() {
    this.userService.logout();
    this.router.navigate(['/']);
  }
}
