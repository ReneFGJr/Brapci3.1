import { Component, OnInit } from '@angular/core';
import { CollectionsService } from 'src/app/services/collections.service';

@Component({
  selector: 'app-data-collections',
  templateUrl: './data-collections.component.html',
  styleUrls: ['./data-collections.component.scss']
})
export class DataCollectionsComponent implements OnInit {


  CollectionsData = [];

  constructor(
    private collectionsService: CollectionsService
  ) { }

  ngOnInit()
    {
      this.collectionsService.getCollections().subscribe(
        (data)=> {
          this.CollectionsData = [];
          console.log(data);
        },
        (error)=>{
          console.log(error);
        }
      );
    }
}
