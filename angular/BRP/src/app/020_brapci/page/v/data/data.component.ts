import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-v-data',
  templateUrl: './data.component.html',
  styleUrls: ['./data.component.scss'],
})
export class DataVComponent {
  @Input() public RDFdata: Array<any> | any;
  ngOnInit()
    {
      console.log(this.RDFdata);
    }

}
