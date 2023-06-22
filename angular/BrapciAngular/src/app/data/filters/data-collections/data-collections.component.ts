import { Component, OnInit } from '@angular/core';
import { CollectionsService } from 'src/app/services/collections.service';

@Component({
  selector: 'app-data-collections',
  templateUrl: './data-collections.component.html',
  styleUrls: ['./data-collections.component.scss']
})

export class DataCollectionsComponent implements OnInit {

  CollectionsData: Array<any> = new Array();
  CollectionSize: number = 0;

  constructor(
    private collectionsService: CollectionsService
  ) { }

  ngOnInit()
    {
      this.collectionsService.getCollections().subscribe(CollectionsData => {
          this.CollectionsData = CollectionsData;
          this.CollectionSize = CollectionsData.length;
        },
        (error)=>{
          console.log(error);
        }
      );
    }
}
