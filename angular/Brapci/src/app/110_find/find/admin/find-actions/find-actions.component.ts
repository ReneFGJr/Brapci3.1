import { Component } from '@angular/core';

@Component({
  selector: 'app-find-actions',
  templateUrl: './find-actions.component.html',
  styleUrls: ['./find-actions.component.css']
})
export class FindActionsComponent {
  public actions = [
        { 'service': '/books/admin/', 'name':'home' },
        { 'service':'/books/admin/isbn/add/','name':'Add ISBN'}
        ];

}
