import { Component } from '@angular/core';
import { environment } from 'environments/environment';

@Component({
  selector: 'app-foot',
  templateUrl: './foot.component.html',
  styleUrls: ['./foot.component.scss']
})
export class FootComponent {
  HTTP = `${environment.HTTP}`;
}
