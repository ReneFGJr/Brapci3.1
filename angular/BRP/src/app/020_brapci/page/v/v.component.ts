import { Component } from '@angular/core';
import { BrapciService } from '../../../000_core/010_services/brapci.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-v',
  templateUrl: './v.component.html',
  styleUrls: ['./v.component.scss']
})
export class VComponent {
  public type: string = 'Article';
  public data: Array<any> | any
  public sub: Array<any> | any
  public id: number = 0;

  constructor(
    private brapciService: BrapciService,
    private route: ActivatedRoute,
  ) { }

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
