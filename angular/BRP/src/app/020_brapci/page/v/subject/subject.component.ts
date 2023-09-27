import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-subject',
  templateUrl: './subject.component.html',
  styleUrls: ['./subject.component.scss'],
})
export class SubjectVComponent {
  @Input() public data: Array<any> | any;
  ngOnInit() {
    console.log(this.data);
  }
}
