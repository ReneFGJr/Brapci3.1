import { Component } from '@angular/core';
import { BrapciService } from 'src/app/020_brapci/service/brapci.service';

@Component({
  selector: 'app-main-sources',
  templateUrl: './main-sources.component.html',
  styleUrls: ['./main-sources.component.scss']
})
export class MainSourcesComponent {
  public sources: Array<any> | any
  constructor(
    private brapciService: BrapciService
  ) {}

  collection(journal: any[],type: string=''): any {
    return journal.filter(p => p.jnl_collection === type);
  }

  ngOnInit()
    {
      this.brapciService.sources('EV').subscribe(
        res=>
        {
          this.sources = res
        }
      )
    }
}
