import { Component } from '@angular/core';

@Component({
  selector: 'app-index',
  templateUrl: './index.component.html',
})
export class IndexEventComponent {
  public header: string = 'Header';
  public data:Array<any> | any = []
}
