import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-search-brapci-results',
  templateUrl: './search-brapci-results.component.html',
  styleUrls: ['./search-brapci-results.component.scss']
})
export class SearchBrapciResultsComponent {
  @Input() public results: Array<any> | any;

  ngOnInit()
    {

    }
}
