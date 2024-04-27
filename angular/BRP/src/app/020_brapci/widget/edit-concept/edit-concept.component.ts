import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'app-edit-concept',
  templateUrl: './edit-concept.component.html',
})
export class EditConceptComponent {
  @Input() ID: string = '';

  public userID: Array<any> | any = null;

  constructor(
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService
  ) {}

  ngOnInit() {
    this.userID = this.localStorageService.get('user');
  }
}
