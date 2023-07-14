import { Component } from '@angular/core';
import { UserService } from 'src/app/001_auth/service/user.service';
import { AuthorityService } from '../../service/authority.service';

@Component({
  selector: 'app-authority-search-home',
  templateUrl: './search-home.component.html',
  styleUrls: ['./search-home.component.scss']
})
export class SearchAuthorityHomeComponent {
  public items: Array<any> | any;
  public nomes: Array<any> | any;
  public total: number = 0;
  public pag: number = 0;

  constructor(
    private userService: UserService,
    private authorityService: AuthorityService,
  ) { }

  searchItens(term: string, type: string) {
    this.authorityService.searchList(term, type).subscribe(
      res => {
        this.items = res;
        this.nomes = this.items.data.item;
      }
    )
  }

}
