import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-view-source',
  templateUrl: './view-source.component.html',
  styleUrls: ['./view-source.component.scss']
})
export class ViewSourceComponent {
  [x: string]: any;

  public publication: Array<any> | any
  public issue: Array<any> | any
  public id: number = 0;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private brapciService: BrapciService
  ) { }


  viewIssue(id:string)
    {
      this.router.navigate(['sources/issue/'+id]);
    }

  ngOnInit() {
      this.route.params.subscribe(params => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      this.brapciService.source(this.id).subscribe(
        res=>
        {
          this.publication = res;
          this.issue = this.publication.source.issue
          console.log(this.issue)
        }
      )
    });
  }
}
