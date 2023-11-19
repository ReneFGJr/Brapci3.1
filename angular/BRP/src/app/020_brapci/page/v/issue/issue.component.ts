import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-issue',
  templateUrl: './issue.component.html',
})
export class IssueBrapciComponent {
  @Input() public data: Array<any> | any;
  public url: string = '';
  public header: Array<any> | any = null;
  public section = [{ name: 'DATA' }];
}
