import { HttpClient } from '@angular/common/http';
import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-rdf-delete',
  templateUrl: './delete.component.html',
})
export class DeleteRDFComponent {
  busy: string = '';
  data: Array<any> | any;
  sub: Array<any> | any;
  temp: Array<any> | any;
  ID: string = '';
  propriety: string = '';
  message: string = '';

  constructor(
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private router: Router,
    private route: ActivatedRoute,
    private http: HttpClient
  ) {}

  ngOnInit() {
    this.sub = this.route.params.subscribe((params) => {
      this.ID = params['id']; // (+) converts string 'id' to a number
      this.propriety = params['prop']; // (+) converts string 'id' to a number
      {
        let url = 'brapci/data/' + this.ID;
        this.brapciService.api_post(url).subscribe((res) => {
          this.data = res;
          console.log(res);
        });
      }
    });
  }

  exclude() {
    let url = 'rdf/delData/' + this.ID;
    this.brapciService.api_post(url).subscribe((res) => {
      this.temp = res;
      this.message = this.temp.message;
      console.log(this.temp.status);
      if (this.temp.status == '200') {
        window.opener.location.reload();
        window.self.close();
      } else {
        this.message = this.temp.message;
      }
    });
  }
}
