import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-journal-view',
  templateUrl: './journal-view.component.html',
  styleUrls: ['./journal-view.component.scss']
})
export class JournalViewComponent {
  public params: Array<any>|any
  public journal: Array<any> | any
  public page:string = '1';

  constructor(
    private activatedRoute: ActivatedRoute,
    private brapciService: BrapciService,
    ) {
      this.page = '1';
    }

  selectPG(pg:string)
    {
      this.page = pg;
    }

  ngOnInit() {
    this.activatedRoute.params.subscribe(
      res => {
        this.params = res;
        this.brapciService.sources(this.params.id).subscribe(
          res=>{
            this.journal = res;
            this.journal = this.journal.source;
          }
        )
      }
    )
  }
}
