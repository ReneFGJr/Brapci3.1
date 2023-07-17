import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-item-view-person',
  templateUrl: './item-view-person.component.html',
  styleUrls: ['./item-view-person.component.scss']
})
export class ItemViewPersonComponent {
  @Input() public person: Array<any> | any
  public genere = 'bi-gender-female';
  /* bi-gender-male bi-gender-ambiguous */
  ngOnInit()
    {
      console.log("**********")
      console.log(this.person)
    }
}
