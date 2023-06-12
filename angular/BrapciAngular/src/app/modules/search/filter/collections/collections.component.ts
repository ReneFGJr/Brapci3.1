import { Component, OnInit } from '@angular/core';
import { CollectionsService } from 'src/app/services/collections.service';

@Component({
  selector: 'app-collections',
  templateUrl: './collections.component.html',
  styleUrls: ['./collections.component.scss']
})
export class CollectionsComponent implements OnInit {
  constructor(
    private CollectionsService: CollectionsService
  ) { }

  collections:String = 'Coleções';
  CollectionsData = [{name:"Nome"}];


  ngOnInit()
    {
      this.CollectionsService.getCollections().subscribe(
        (data)=> {
          console.log(data);
          //this.CollectionsData = data;
          console.log(this.CollectionsData);
        },
        (error)=>{
          console.log(error);
        }
      );

    }
}
