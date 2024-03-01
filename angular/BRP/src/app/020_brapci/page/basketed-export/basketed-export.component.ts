import { Component, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'app-basketed-export',
  templateUrl: './basketed-export.component.html',
})
export class BasketedExportComponent {
  @Input() public row: Array<any> | any;
  public sub: Array<any> | any;
  public basket: Array<any> | any;
  public total: number = 0;

  constructor(
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      //this.row = params['id'] // (+) converts string 'id' to a number
      console.log(params);
    });
  }

  export(typeE: string) {
    this.basket = this.localStorageService.get('marked');

    if (this.basket == null) {
      this.basket = [];
    }

    this.total = this.basket.length;

    if (this.total > 0) {
      let dt: Array<any> | any = { row: this.basket };
      this.brapciService
        .api_post('brapci/export/' + typeE, dt)
        .subscribe((res) => {
          this.row = res;
          this.downloadFile(this.row.download);
        });
    }
  }

 downloadFile(filePath:string){
    var link=document.createElement('a');
    link.href = filePath;
    link.download = filePath.substr(filePath.lastIndexOf('/') + 1);
    link.click();
}
}
