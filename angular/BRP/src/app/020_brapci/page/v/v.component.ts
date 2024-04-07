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
          if (this.data.Issue != undefined) {
            if (this.data.Issue.jnl_rdf == 75) {
              this.type = 'Benancib';
            } else if (this.data.Issue.jnl_rdf == 18) {
              //this.type = 'EBBC';
            }
            console.log('TYPE:' + this.data.Issue.jnl_rdf);
          }

          console.log("TYPE=>"+this.data.Class)

          if (this.type == 'Person') {
            this.header.title = this.data.name + ' | ' + ' Autor';
          } else {
            if (this.type == 'Issue') {
              if (this.data.is_vol_roman == '')
                {
                  this.header.title =
                    this.data.publisher +
                    ' | ' +
                    this.data.is_year +
                    ' | ' +
                    this.data.is_vol +
                    ' | ' +
                    this.data.is_nr
                    ;
                } else {
                  this.header.title =
                    this.data.is_vol_roman + ' ' +this.data.publisher +
                    ' | ' +
                    this.data.is_year;
                }

            } else {

            }
          }
        },
        (error) => error
      );
    });
  }
}
