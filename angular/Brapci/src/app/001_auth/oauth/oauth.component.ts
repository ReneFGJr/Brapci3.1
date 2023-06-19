import { Component } from '@angular/core';
import { UserService } from '../service/user.service';

@Component({
  selector: 'app-oauth',
  templateUrl: './oauth.component.html',
  styleUrls: ['./oauth.component.css']
})
export class OauthComponent {
  public login = true;

  constructor(private UserService: UserService) {}

  ngOnInit() {
        console.log(UserService);
        console.log("HELLO");

    }
}
