import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-v-author',
  templateUrl: './author.component.html',
  styleUrls: ['./author.component.scss'],
})
export class AuthorVComponent {
  @Input() public data: Array<any> | any;
  ngOnInit() {
    console.log(this.data);
  }
}
