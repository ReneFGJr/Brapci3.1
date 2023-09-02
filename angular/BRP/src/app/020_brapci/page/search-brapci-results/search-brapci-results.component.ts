import { Router } from '@angular/router';
import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-search-brapci-results',
  templateUrl: './search-brapci-results.component.html',
  styleUrls: ['./search-brapci-results.component.scss']
})
export class SearchBrapciResultsComponent {
  @Input() public results: Array<any> | any;
  listArray: string[] = [];
  sum = 1;
  display = 10;
  direction = "";

  constructor() {
    if (this.results != undefined)
    {
      console.log('===============>'+this.results);
    }
  }

  ngOnInit()
    {
      if (this.results != undefined)
      {
        console.log('===============>'+this.results);
      }
      
      //this.listArray.push(this.results[0]);
    }


  onScrollDown(ev: any) {
    console.log("scrolled down!!", ev);
    this.appendItems();

    this.direction = "scroll down";
  }

  onScrollUp(ev: any) {

  }

  appendItems() {
    this.addItems("push");
  }

  addItems(_method: string) {
    for (let i = 0; i < 1; ++i) {
      if (_method === 'push') {
        this.listArray.push([this.sum].join(""));
        this.sum++;
      }
    }
  }
}
