import { BrapciService } from 'src/app/020_brapci/service/brapci.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-journal-welcome',
  templateUrl: './journal-welcome.component.html',
  styleUrls: ['./journal-welcome.component.scss']
})
export class JournalWelcomeComponent {

  public journals:Array<any>|any
  constructor(
    private brapciService: BrapciService,
  ) {}

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
