import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-work-sumary',
  templateUrl: './sumary.component.html',
  styleUrls: ['./sumary.component.scss'],
})
export class SumaryComponent {
  @Input() public data: Array<any> | any;
  constructor(private router: Router) {}

  redirect(ID: string) {
    this.router.navigate(['/v/' + ID]);
  }
}
