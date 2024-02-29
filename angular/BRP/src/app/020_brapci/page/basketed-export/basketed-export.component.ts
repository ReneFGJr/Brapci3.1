import { Component, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-basketed-export',
  templateUrl: './basketed-export.component.html',
})
export class BasketedExportComponent {
  @Input() public row: Array<any> | any;
  public sub: Array<any> | any;

  constructor(
    public brapciService: BrapciService,
    private route: ActivatedRoute,
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      //this.row = params['id'] // (+) converts string 'id' to a number
      console.log(params)
    });
  }
}
