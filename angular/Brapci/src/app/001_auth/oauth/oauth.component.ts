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

  constructor(
    private UserService: UserService,
    private Router: Router
  ) {}

  ngOnInit() {
        if (this.UserService.getUser())
            {
                this.login = false;
                //this.router.navigate(['/']);
            } else {
                console.log('NOT LOGED');
            }
    }
}
