import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-view-authority',
  templateUrl: './view-authority.component.html',
  styleUrls: ['./view-authority.component.css']
})
export class ViewAuthorityComponent {
  public userId: number = 0;

  constructor(private route: ActivatedRoute) {
    this.route.params.subscribe(params => this.userId = params['id']);
  }
}
