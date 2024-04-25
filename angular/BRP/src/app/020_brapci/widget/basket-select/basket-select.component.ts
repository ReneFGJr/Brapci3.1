import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';
import { UserService } from 'src/app/001_auth/service/user.service';

@Component({
  selector: 'app-basket-select',
  templateUrl: './basket-select.component.html',
})
export class BasketSelectComponent {
  @Input() public data: Array<any> | any
  public basketValue: Array<any> | any
  public total: number = 0;
  public total_result: number = 0;

  constructor(
    private userService: UserService,
    private router: Router,
    private localStorageService: LocalStorageService
  ) {}

  ngOnInit() {
    console.log(this.data);
    this.total_result = this.data.length;
  }

  selectAll() {
    /* Recupera Selecionados */
    this.basketValue = this.localStorageService.get('marked');
    if (this.basketValue == null) {
      this.basketValue = [];
    }
    for (let i = 0; i <= this.data.length; i++) {
      if (this.data[i] != null) {
        let ID = this.data[i]['ID'];
        console.log(ID)
        let index = this.basketValue.indexOf(ID);
        if (index >= 0) {
          /* Já existe */
        } else {
          /* Incorpora */
          this.basketValue.push(ID);
        }
      }
    }
    this.localStorageService.set('marked', this.basketValue);
    this.total = this.basketValue.length;
    this.total_result = 0;
  }
  clear() {
    alert('Clear');
  }

  showSelected() {
    this.router.navigate(['basket/selected']);
  }
}
