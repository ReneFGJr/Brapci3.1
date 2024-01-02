import { Component } from '@angular/core';
import { BrapciService } from '../../../000_core/010_services/brapci.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-v',
  templateUrl: './v.component.html',
})
export class VComponent {
  public type: string = 'NA';
  public data: Array<any> | any;
  public sub: Array<any> | any;
  public id: number = 0;
  public header = { title: 'Brapci' };

  constructor(
    private brapciService: BrapciService,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.id = +params['id']; // (+) converts string 'id' to a number

      this.brapciService.getId(this.id).subscribe(
        (res) => {
          this.data = res;
          this.type = this.data.Class;
          if (this.data.Issue.jnl_rdf == 199828)
          {
            this.type = 'Benancib';
          }
          this.header.title = this.data.title + ' | ' + this.data.Authors;
        },
        (error) => error
      );
    });
  }
}
