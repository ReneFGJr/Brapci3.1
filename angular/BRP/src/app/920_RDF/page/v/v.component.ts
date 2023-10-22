import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-rdf-v',
  templateUrl: './v.component.html',
  styleUrls: ['./v.component.scss'],
})
export class RDFVComponent {
  constructor(
    public brapciService: BrapciService,
    private route: ActivatedRoute
  ) {}
  public data: Array<any> | any;
  public sub: Array<any> | any;

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.brapciService.generic('rdf/get/' + params['id']).subscribe(
        (res) => {
          this.data = res;
        },
        (error) => error
      );
    });
  }
}
