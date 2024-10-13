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
  public loveitValue: number = 0;

  constructor(public brapciService: BrapciService) {}

  changeit() {
    if (this.loveitValue == 0)
      {
        this.loveit = '/assets/icone/loveit-pulse.svg';
        this.loveitValue = 1
        let dt: Array<any> | any = { id: this.id };
        console.log(dt)
        this.brapciService.api_post('like/liked',dt).subscribe((res) => {
          console.log(res)
        });
      } else {
        this.loveit = '/assets/icone/love-it-off.svg';
        this.loveitValue = 0;
      }
  }
}
