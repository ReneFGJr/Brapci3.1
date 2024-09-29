import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-timeline-journal',
  templateUrl: './timeline-journal.component.html',
  styleUrls: ['./timeline-journal.component.scss'],
})
export class TimelineJournalComponent {
  @Input() data: Array<any> | any;
}
