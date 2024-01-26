import { Router, RouterLink } from '@angular/router';
import { LocalStorageService } from './../../../001_auth/service/local-storage.service';
import { Component, Input, Output, EventEmitter, SimpleChanges } from '@angular/core';

@Component({
  selector: 'brapci-basket',
  templateUrl: './basket.component.html',
})
export class BasketComponent {
  @Input() public total: number = 0;
  @Input() public result: Array<any> = []
  @Output() basketRow = new EventEmitter()
  public basketValue: Array<any> | any;
  public total_result = 0;


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
    /* Atualiza total dos resultados da busca */
    if (this.result == null)
      {
        this.total_result = 0;
      } else {
        this.total_result = this.result.length;
      }

  }

  selectAll()
    {
      /* Recupera Selecionados */
      this.basketValue = this.localStorageService.get('marked');
      if (this.basketValue == null) { this.basketValue = [] }
      for (let i = 0; i <= this.result.length; i++) {
        if (this.result[i] != null) {
          let ID = this.result[i]['id'];
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
      this.total_result = 0
    }

  ngOnChanges(changes: SimpleChanges): void {
    //Called before any other lifecycle hook. Use it to inject dependencies, but avoid any serious work here.
    //Add '${implements OnChanges}' to the class.
    this.total_result = this.result.length;
    console.log('MOV');
  }

  showSelected() {
    this.router.navigate(['basket/selected']);
  }

  clear() {
    this.localStorageService.remove('marked');
    this.basketRow.emit([]);
  }
}
