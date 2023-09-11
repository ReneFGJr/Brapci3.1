import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-journal-welcome',
  templateUrl: './journal-welcome.component.html',
  styleUrls: ['./journal-welcome.component.scss']
})
export class JournalWelcomeComponent {

  public page:string = '1';
  public journals:Array<any>|any
  constructor(
    private brapciService: BrapciService,
  ) {}

  selectPG(pg: string)
    {
    this.page = pg;
    }

  ngOnInit()
    {
    this.brapciService.sources('journal').subscribe(
        res=>
          {
            console.log(res);
            this.journals = res;
          }
      )
    }

}
