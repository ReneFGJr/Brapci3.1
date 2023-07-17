import { Component, Input } from '@angular/core';
import { BrapciService } from '../../service/brapci.service';

@Component({
  selector: 'app-brapci-banner',
  templateUrl: './banner.component.html',
  styleUrls: ['./banner.component.scss']
})
export class BannerComponent {
  public results: Array<any> | any
  @Input() public title:string = '';

  constructor(
    private brapciService: BrapciService,
  ) { }

  search(term: string)
    {
    this.brapciService.search(term).subscribe(
      res => {
        this.results = res
        console.log("=============")
        console.log(res)
      }
    );
    }
}
