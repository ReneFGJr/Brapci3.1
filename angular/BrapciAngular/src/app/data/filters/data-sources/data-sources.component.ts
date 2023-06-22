import { Component, OnInit } from '@angular/core';
import { SourcesService } from 'src/app/services/sources.service';

@Component({
  selector: 'app-data-sources',
  templateUrl: './data-sources.component.html',
  styleUrls: ['./data-sources.component.scss']
})

export class DataSourcesComponent implements OnInit {
  SourceData: Array<any> = new Array();
  SourceSize: number = 0;

  constructor(
    private sourceService: SourcesService
  ) { }

  ngOnInit() {
      this.sourceService.getSources().subscribe(SourceData => {
      this.SourceData = SourceData;
      this.SourceSize = SourceData.length;
    },
      (error) => {

      }
    );
  }
}
