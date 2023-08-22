import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-oai-buttons',
  templateUrl: './oai-buttons.component.html',
  styleUrls: ['./oai-buttons.component.scss']
})
export class OaiButtonsComponent {
  @Input() public sources:Array<any> | any
  public size:number = 210;

  onSize()
    {
      this.size = this.size + 30
    }
}
