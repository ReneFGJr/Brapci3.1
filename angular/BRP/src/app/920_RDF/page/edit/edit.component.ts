import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';


@Component({
  selector: 'app-rdf-edit',
  templateUrl: './edit.component.html',
  styleUrls: ['./edit.component.scss'],
})
export class EditRDFComponent {
  public type: string = 'NA';
  public data: Array<any> | any;
  public group: Array<any> | any;
  public sub: Array<any> | any;
  public chaves: Array<any> | any;
  public id: number = 0;
  public header = { title: 'Brapci' };

  constructor(
    private brapciService: BrapciService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      let url = 'rdf/a/' + this.id;
      this.brapciService.api_post(url).subscribe((res) => {
        this.data = res;
        this.group = this.data.group;
        console.log(this.data);
      });
    });
  }
}
