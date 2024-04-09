import { Component, Input } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'app-remove-concept',
  templateUrl: './remove-concept.component.html',
  styleUrls: ['./remove-concept.component.scss'],
})
export class RemoveConceptComponent {
  @Input() ID:string = ''
  public userID: Array<any> | any = null;

  constructor(
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService
  ) {}

  deleteRegister() {
    if (confirm('Excluir') == true) {
      let dt: Array<any> | any = { token: this.userID[0].token };
      this.brapciService.api_post('rdf/deleteConcept/'+this.ID, dt).subscribe((res) => {
        console.log(res);
      });
    }
  }
  ngOnInit()
    {
      this.userID = this.localStorageService.get('user');
    }
}
