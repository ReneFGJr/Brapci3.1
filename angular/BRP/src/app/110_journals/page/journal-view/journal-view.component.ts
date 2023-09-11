import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-journal-view',
  templateUrl: './journal-view.component.html',
  styleUrls: ['./journal-view.component.scss']
})
export class JournalViewComponent {
  public params: Array<any>|any
  public journal: Array<any> | any
  public page:string = '1';
  public issue:Array<any>|any

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
            this.issue = this.journal.issue;
          }
        )
      }
    )
  }
}
