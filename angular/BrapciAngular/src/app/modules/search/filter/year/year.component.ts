import { Component } from '@angular/core';

@Component({
  selector: 'app-year',
  templateUrl: './year.component.html',
  styleUrls: ['./year.component.scss']
})
export class YearComponent {

  public filterCollections = true;

constructor() {}

public years()
  {
    return "ANOS";
  }

}
