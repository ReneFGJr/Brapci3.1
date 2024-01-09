import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-index-subject',
  templateUrl: './index-subject.component.html',
})
export class IndexSubjectComponent {
  @Input() public data: Array<any> | any;

 constructor(private router: Router) { }

  public ltrs: Array<any> = ['A', 'B', 'C', 'D' ,'E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','X','Y','W','Z'];

  goUrl(url:string)
  {
    this.router.navigate([url]);
  }

  ngOnChange()
    {
      console.log("NEW-CHANGE")
    }
}
