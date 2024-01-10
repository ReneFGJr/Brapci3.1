import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-index-subject',
  templateUrl: './index-subject.component.html',
})
export class IndexSubjectComponent {
  @Input() public data: Array<any> | any;

  ngOnChange()
    {
      console.log("NEW-CHANGE")
    }
}
