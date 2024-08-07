import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-proceeding-issues',
  templateUrl: './proceeding-issues.component.html',
  styleUrls: ['./proceeding-issues.component.scss'],
})
export class ProceedingIssuesComponent {
  constructor(
    private route: ActivatedRoute,
    private brapciService: BrapciService,
    private router: Router
  ) {}

  public id: number = 0;
  public source: Array<any> | any;

  ngOnInit() {
    this.route.params.subscribe((params) => {
      this.id = +params['id']; // (+) converts string 'id' to a number
      console.log(this.id);
      this.brapciService.source(this.id).subscribe((res) => {
        this.source = res;
      });
    });
  }

  viewIssue(id:string)
    {
      this.router.navigate(['proceedings/issue/'+id]);
    }
}
