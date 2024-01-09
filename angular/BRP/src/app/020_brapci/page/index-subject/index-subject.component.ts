import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-index-subject',
  templateUrl: './index-subject.component.html'
})
export class IndexSubjectComponent {
  @Input() public data: Array<any> | any;
}
