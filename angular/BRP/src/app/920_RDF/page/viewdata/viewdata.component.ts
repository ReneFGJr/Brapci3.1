import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-rdf-viewdata',
  templateUrl: './viewdata.component.html',
})
export class RDFViewdataComponent {
  constructor(
    public brapciService: BrapciService,
    private route: ActivatedRoute
  ) {}
  public data: Array<any> | any;
  public dataview: Array<any> | any;
  public sub: Array<any> | any;

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      /********************************** Info */
      this.brapciService.api('rdf/getdata/' + params['id']).subscribe(
        (res) => {
          console.log('==z==');
          this.dataview = res;
          console.log(this.dataview);
        },
        (error) => error
      );
    });
  }
}
