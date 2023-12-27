import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-brapci-filestorage',
  templateUrl: './filestorage.component.html',
})
export class FilestorageComponent {
  @Input() public data: Array<any> | any;
}
