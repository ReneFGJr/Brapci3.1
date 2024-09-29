import { Component, OnInit } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-timeline',
  templateUrl: './timeline.component.html',
  styleUrls: ['./timeline.component.scss'],
})
export class TimelineComponent {
  public type: string = 'NA';
  public data: Array<any> | any;
  public sub: Array<any> | any;
  public chaves: Array<any> | any;
  public id: number = 0;
  public header = { title: 'Timeline - Journals' };

  constructor(private brapciService: BrapciService) {}
  ngOnInit() {
    this.brapciService.api_post('brapci/timeline').subscribe((res) => {
      this.data = res;
      console.log(this.data);
    });
  }
}
