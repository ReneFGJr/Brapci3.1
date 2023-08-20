import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-view-source',
  templateUrl: './view-source.component.html',
  styleUrls: ['./view-source.component.scss']
})
export class ViewSourceComponent {

  public resource: Array<any> | any
  public id: number = 0;
  private sub: any;

  constructor(
    private route: ActivatedRoute,
    private brapciService: BrapciService
  ) { }

  ngOnInit() {
    this.sub = this.route.params.subscribe(params => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      this.brapciService.source(this.id).subscribe(
        res=>
        {
          this.resource = res;
        }
      )
    });
  }
}
