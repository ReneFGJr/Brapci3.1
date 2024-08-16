import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-work-sumary',
  templateUrl: './sumary.component.html',
  styleUrls: ['./sumary.component.scss']
})
export class SumaryComponent {
  @Input() public data:Array<any> |any
}
