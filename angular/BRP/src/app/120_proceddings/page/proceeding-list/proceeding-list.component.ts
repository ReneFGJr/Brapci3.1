import { Component } from '@angular/core';
import { ActivatedRoute, Route, Router, RouterLink } from '@angular/router';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-proceeding-list',
  templateUrl: './proceeding-list.component.html',
})
export class ProceedingListComponent {
  public sources: Array<any> | any;
  constructor(
    private brapciService: BrapciService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.brapciService.sources('E').subscribe((res) => {
      this.sources = res;
      console.log(res);
    });
  }

  viewProceeding(id: string) {
    this.router.navigate(['proceedings/issues/' + id]);
  }
}
