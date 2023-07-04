import { Component } from '@angular/core';
import { UserService } from '../service/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-oauth',
  templateUrl: './oauth.component.html',
  styleUrls: ['./oauth.component.css']
})
export class OauthComponent {
  public login = true;
  public user: Array<any> | any

  constructor(
    private UserService: UserService,
    private router: Router
  ) { }

  ngOnInit() {
    this.user = <any>this.UserService.getUser()
    if (this.user['token']) {
      this.login = false;
      this.router.navigate(['/']);
      console.log('LOGED');
    } else {
      console.log('NOT LOGED');
    }
  }
}
