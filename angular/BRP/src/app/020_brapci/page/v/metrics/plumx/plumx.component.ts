import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-altmetrix-plumx',
  templateUrl: './plumx.component.html',
  styleUrls: ['./plumx.component.scss'],
})
export class PlumxComponent {
  @Input() public doi: string = '';
}
