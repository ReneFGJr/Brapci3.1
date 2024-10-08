import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-person-photo',
  templateUrl: './photo.component.html',
  styleUrls: ['./photo.component.scss'],
})
export class PhotoComponent {
  @Input() public data: Array<any> | any;
}
