import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-view-issue',
  templateUrl: './view-issue.component.html',
  styleUrls: ['./view-issue.component.scss'],
})
export class ViewIssueComponent {
  public sources: Array<any> | any;
  public id:number = 0;

  constructor(
      private brapciService: BrapciService,
      private router: Router,
      private route: ActivatedRoute,
    ) {}

  ngOnInit() {
    this.route.params.subscribe((params) => {
      this.id = +params['id']; // (+) converts string 'id' to a number

      console.log('HELLO');
      this.brapciService.getIssue(this.id).subscribe(
        res=>{
          this.sources = res
        }
      );
    });
  }
}
