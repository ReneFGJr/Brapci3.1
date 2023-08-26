import { Component } from '@angular/core';
import { FormBuilder, FormGroup, NgForm, Validators } from '@angular/forms';

import { AuthorityService } from '../../service/authority.service';
import { UserService } from 'src/app/001_auth/service/user.service';
import { SearchAuthorityHomeComponent } from '../search-home/search-home.component';

@Component({
  selector: 'app-authority-search',
  templateUrl: './search.component.html'
})
export class SearchAuthorityComponent {
  public term: string = '';

  constructor(
    private userService: UserService,
    private authorityService: AuthorityService,
    private searchAuthorityHomeComponent: SearchAuthorityHomeComponent,
    private fb: FormBuilder,
   ) {}

  /************************************************************************  */
  setDefault(f: NgForm) {
    f.resetForm({
      term: 'Hello',
    })
  }

  ngOnInit()
    {
        this.createForm();
    }

  /************************************************************************  */
  angForm: FormGroup | any;
  createForm() {
    this.angForm = this.fb.group({
      term: ['', Validators.required]
    });
  }


  onSearch() {
    if (this.angForm.valid) {
      let term = this.angForm.value.term
      let type = '1'
      this.searchAuthorityHomeComponent.searchItens(term,type);
    }
  }

  onKeyPress()
    {

    }
}
