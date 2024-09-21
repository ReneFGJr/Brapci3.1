import { Component, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-brapci-journal',
  templateUrl: './journal.component.html',
  styleUrls: ['./journal.component.scss'],
})
export class JournalComponent {
  @Input() public data: Array<any> | any;
  public page: string = '0';
  public params: Array<any> | any;

  constructor(
    private activatedRoute: ActivatedRoute,
  ) {
    this.page = '1';
  }

  selectPG(pg: string) {
    this.page = pg;
  }

  ngOnInit() {
    this.activatedRoute.params.subscribe((res) => {
      this.params = res;
    });
  }
}
