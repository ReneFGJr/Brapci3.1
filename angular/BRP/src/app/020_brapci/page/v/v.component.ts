import { Component } from '@angular/core';
import { BrapciService } from '../../service/brapci.service';

@Component({
  selector: 'app-v',
  templateUrl: './v.component.html',
  styleUrls: ['./v.component.scss']
})
export class VComponent {
  public type:string = 'Article';
  public data:Array<any> | any
  public id:string = '1433';

  constructor(
    private brapciService: BrapciService
  ) {}

  ngOnInit()
    {
      console.log("GET DATA "+this.id)
      this.brapciService.getId(this.id).subscribe(
        res=>
          {
            this.data = res;
          },
        error=>error
      )
    }

}
