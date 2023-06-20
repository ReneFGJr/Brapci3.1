import { UIuser } from './../../interface/UIusers';
import { Component } from '@angular/core';
import { UserService } from '../../service/user.service';

@Component({
  selector: 'app-user-perfil',
  templateUrl: './user-perfil.component.html',
  styleUrls: ['./user-perfil.component.css']
})
export class UserPerfilComponent {
  constructor(private UserService: UserService) {}
  public user: Array<UIuser> = this.UserService.user;

  ngOnInit() {
    console.log(this.user);
  }
}
