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
      this.brapciService.generic('rdf/get/' + params['id']).subscribe(
        (res) => {
          this.data = res;
        },
        (error) => error
      );

      console.log("HELLO");
      this.brapciService.api_post('rdf/getdata/' + params['id']).subscribe(
        (res) => {
          this.dataview = res;
          console.log(res);
        },
        (error) => error
      );
      console.log('HELLO2');
    });
  }
}
