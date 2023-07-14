import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { AuthorityService } from '../../service/authority.service';

@Component({
  selector: 'app-item-view',
  templateUrl: './item-view.component.html',
})
export class ItemViewComponent {
  public person: Array<any> | any
  public id: number = 0;
  private sub: any;

  constructor(
    private route: ActivatedRoute,
    private authorityService: AuthorityService
  ) { }

  ngOnInit() {

    this.sub = this.route.params.subscribe(params => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      console.log(this.id);
    });

    this.authorityService.getId(this.id).subscribe(
      res => {
        this.sub = res;
        this.person = this.sub.data
        console.log(res)
      }
    )

  }
  /*
  ngOnInit() {
    this.sub = this.route.params.subscribe(params => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      console.log(this.id);
    });
  }
  */

}
