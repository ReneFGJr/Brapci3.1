import { Component } from '@angular/core';

@Component({
  selector: 'app-filter',
  templateUrl: './filter.component.html',
  styleUrls: ['./filter.component.scss']
})
export class FilterComponent {
  public FilterLabel = "Filtros";
  public filterCollection: boolean = false;
  public filterYear: boolean = false;
  public Status: string = "none";

  ngOnInit(): void {
      setInterval(() =>
      {
        this.Status = 'Year ' + this.filterYear + ' | Collection '+this.filterCollection;
      },2000)
  }

  public toggleShowYear()
    {
      if (this.filterYear) {
        this.filterYear = false;
      } else {
        this.filterYear = true;
      }
    }

  public toggleShowCollection() {
    if (this.filterCollection) {
      this.filterCollection = false;
    } else {
      this.filterCollection = true;
    }
  }
}
