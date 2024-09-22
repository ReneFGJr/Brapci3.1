import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-icr-producao-journal-author',
  templateUrl: './icr-producao-journal-author.component.html',
})
export class IcrProducaoJournalAuthorComponent {
  @Input() public jid: string = '';

  public data: Array<any> | any;

  public categories: Array<any> | any;
  public seriesData: Array<any> | any;

  constructor(private brapciService: BrapciService) {}

  ngOnInit(): void {
    this.brapciService
      .api_post('indicator/ProducaoJournalAutores/' + this.jid)
      .subscribe((res) => {
        this.data = res;
        this.data = this.data.data;
      });
  }
}
