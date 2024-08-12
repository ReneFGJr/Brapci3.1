import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-rdf-edit',
  templateUrl: './edit.component.html',
  styleUrls: ['./edit.component.scss']
})
export class EditRDFComponent {

  public type: string = 'NA';
  public data: Array<any> | any;
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
      let url = 'rdf/a/' + this.id
      this.brapciService.api_post(url).subscribe(
        (res) => {
          this.data = res;
          console.log(this.data)
          if (this.data.status == '404') {
            console.log("Registro cancelado")
            this.router.navigate(['404']);
          } else {
            this.type = this.data.Class;
            if (this.data.Issue != undefined) {
              if (this.data.Issue.jnl_rdf == 75) {
                this.type = 'Benancib';
              } else if (this.data.Issue.jnl_rdf == 18) {
                this.type = 'EBBC';
              }
              console.log('TYPE:' + this.data.Issue.jnl_rdf);
            }
          }
        })
    })
  }
}
