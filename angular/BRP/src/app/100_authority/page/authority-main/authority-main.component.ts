import { Component } from '@angular/core';

import { AuthorityService } from '../../service/authority.service';
import { UserService } from 'src/app/001_auth/service/user.service';

@Component({
  selector: 'app-authority-main',
  templateUrl: './authority-main.component.html',
  styleUrls: ['./authority-main.component.scss']
})
export class AuthorityMainComponent {
  public items: Array<any> | any;
  public lista: Array<any> | any;

  constructor(
    private userService: UserService,
    private authorityService: AuthorityService,
  ) { }

  searchItens(term: string, type: string)
    {
      this.authorityService.searchList(term, type).subscribe(
        res => {
          this.items = res;
          this.lista = this.items.data;
        }
      )
    }

}
