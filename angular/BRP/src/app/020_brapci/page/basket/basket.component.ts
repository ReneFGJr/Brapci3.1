import { Router, RouterLink } from '@angular/router';
import { LocalStorageService } from './../../../001_auth/service/local-storage.service';
import { Component, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'brapci-basket',
  templateUrl: './basket.component.html',
})
export class BasketComponent {
  @Input() public total: number = 0;
  @Output() basketRow = new EventEmitter();
  public basketValue: Array<any> | any;

  constructor(
    private localStorageService: LocalStorageService,
    private router: Router
    ) {}
  public selected: number = 0;

  ngOnInit() {
    this.basketValue = this.localStorageService.get('marked');
    if (this.basketValue != null) {
      this.selected = this.basketValue.length;
    } else {
      this.selected = 0;
    }
  }

  showSelected()
    {
        this.router.navigate(['basket/selected']);
    }

  clear() {
    this.localStorageService.remove('marked');
    this.basketRow.emit([]);
  }
}
