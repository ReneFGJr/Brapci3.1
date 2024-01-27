import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-component-loveit',
  templateUrl: './loveit.component.html',
})
export class LoveItComponent {
  @Input() public url: string = '';
  @Input() public id: string = '';

  public loveit: string = '/assets/icone/love-it-off.svg';

  constructor(public brapciService: BrapciService) {}

  changeit() {
    alert("OK")
  }
}
