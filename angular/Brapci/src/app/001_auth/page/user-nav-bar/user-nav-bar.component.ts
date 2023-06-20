import { Component, OnInit } from '@angular/core';
import { UserService } from '../../service/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-user-nav-bar',
  templateUrl: './user-nav-bar.component.html',
  styleUrls: ['./user-nav-bar.component.css']
})
export class UserNavBarComponent {

  public givenName:string = '';
  constructor(private UserService: UserService,
  private router: Router) {}
  public loged = false;

  ngOnInit()
    {
      this.UserService.getUser();
      if (this.UserService.user == null)
      {
        console.log("==NOT LOGED==");
      } else {
        this.loged = true;
        let gn = <any>this.UserService.user;
        this.givenName = gn['givenName'];
      }
    }

  logout()
    {
        this.UserService.logout();
        //this.router.navigate(['/']);
        window.location.reload();
        console.log('EXIT');
    }

  onChangePassword()
    {
        this.router.navigate(['/social/password'])
    }

  viewProfile()
    {
        this.router.navigate(['/social/perfil'])
    }
}
