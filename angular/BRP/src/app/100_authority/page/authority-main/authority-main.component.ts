import { Component } from '@angular/core';

import { AuthorityService } from '../../service/authority.service';
import { UserService } from 'src/app/001_auth/service/user.service';

@Component({
  selector: 'app-authority-main',
  templateUrl: './authority-main.component.html',
  styleUrls: ['./authority-main.component.scss']
})
export class AuthorityMainComponent {
  header = {'header':'Busca por autoridades'}
}
