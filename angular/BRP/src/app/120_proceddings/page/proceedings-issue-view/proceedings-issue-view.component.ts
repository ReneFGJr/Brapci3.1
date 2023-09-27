import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-proceedings-issue-view',
  templateUrl: './proceedings-issue-view.component.html',
  styleUrls: ['./proceedings-issue-view.component.scss'],
})
export class ProceedingsIssueViewComponent {
  constructor(
    private route: ActivatedRoute,
    private brapciService: BrapciService
  ) {}

  public id: number = 0;
  public source: Array<any> | any;

  ngOnInit() {
    this.route.params.subscribe((params) => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      console.log(this.id);
      this.brapciService.issue(this.id).subscribe((res) => {
        this.source = res;
        console.log(res);
      });
    });
  }
}
