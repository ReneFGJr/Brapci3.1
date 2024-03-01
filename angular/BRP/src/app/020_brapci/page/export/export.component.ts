import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'app-export',
  templateUrl: './export.component.html',
})
export class ExportComponent {
  constructor(
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private route: ActivatedRoute
  ) {}
  public type: String = '';
  public total: number = 0;
  public basket: Array<any> | any;
  public row: Array<any> | any;

  ngOnInit() {
    this.route.params.subscribe((params) => {
      this.type = params['id'];

      /* Recupera ID */
    });

    this.basket = this.localStorageService.get('marked');

    if (this.basket == null) {
      this.basket = [];
    }

    this.total = this.basket.length;

    if (this.total > 0) {
      let dt: Array<any> | any = { row: this.basket };
      this.brapciService
        .api_post('brapci/export/' + this.type, dt)
        .subscribe((res) => {
          this.row = res;
        });
    }
  }
}
