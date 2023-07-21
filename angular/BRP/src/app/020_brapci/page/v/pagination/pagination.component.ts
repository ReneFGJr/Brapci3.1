import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-result-pagination',
  templateUrl: './pagination.component.html',
  styleUrls: ['./pagination.component.scss']
})
export class PaginationComponent {
  @Input() public result: Array<any> | any
  public actual: number = 1;
  public offset: number = 0;
  public total: number = 0;
  public token: string = '';
  public pages: Array<any> | any
  public next: number = -1;
  public prev: number = -1;


  ngOnInit() {
    console.log("Pagination")
    this.total = this.result.total
    this.pages = []
    if (this.offset == 0) {
      this.offset = 10;
    }
    let pgs = this.total / this.offset
    console.log(this.total)
    console.log(this.offset)
    console.log(pgs)
    let max = 10;

    for (let i = 1; i <= pgs; i++) {
      if (max > 0)
        {
          this.pages.push(i);
          max -= 1;
          if (max == 0) { this.next = i + 1; }
          console.log(max)
        }
    }
  }
  goPage(pg:string) {
    alert(pg)
  }

}
