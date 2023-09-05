import { Component } from '@angular/core';
import { ActivatedRoute, Route } from '@angular/router';

import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-source-view',
  templateUrl: './source-view.component.html',
  styleUrls: ['./source-view.component.scss']
})
export class SourceViewComponent {
  public sub:Array<any>|any
  public data:Array<any>|any
  public id:number = 0

  constructor(
    private brapciService: BrapciService,
    private route: ActivatedRoute,
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe(params => {
      this.id = + params['id']; // (+) converts string 'id' to a number

      this.brapciService.getId(this.id).subscribe(
        res => {
          this.data = res;
        },
        error => error
      )
    });
  }

}
