import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-date',
  templateUrl: './date.component.html',
})
export class DateComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public header: Array<any> | any = null;
  public section = [{ name: 'DATA' }];

  ngOnInit(): void {
    this.header = [];
    this.header = { title: 'Date' };
    //this.url = this.data.id;
    //console.log(this.data);
    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.
  }
}
