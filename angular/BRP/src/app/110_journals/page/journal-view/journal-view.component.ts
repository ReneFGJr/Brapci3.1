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
  constructor(
    private activatedRoute: ActivatedRoute,
    private brapciService: BrapciService,
    ) { }

  ngOnInit() {
    this.activatedRoute.params.subscribe(
      res => {
        console.log(res)
        this.params = res;
        this.brapciService.sources(this.params.id).subscribe(
          res=>{
            this.journal = res;
          }
        )
      }
    )
  }
}
