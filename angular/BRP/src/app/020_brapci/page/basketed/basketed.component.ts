import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from './../../../001_auth/service/local-storage.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-basketed',
  templateUrl: './basketed.component.html',
  styleUrls: ['./basketed.component.scss']
})
export class BasketedComponent {
  public basket:Array<any>|any
  public total:number = 0;
  constructor(
    private localStorageService:LocalStorageService,
    private brapciService: BrapciService
  ) {}

  ngOnInit()
    {
        this.basket = this.localStorageService.get('marked');
        console.log(this.basket);
        this.total = this.basket.length;

        if (this.total > 0)
          {
            this.brapciService.basket(this.basket)
          }

    }

}
