import { Observable } from 'rxjs/internal/Observable';
import { Component } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-rdf-main-ontology',
  templateUrl: './main-ontology.component.html',
})
export class MainRdfOntologyComponent {
  constructor(public brapciService: BrapciService) {}
  public rdf: Array<any> | any;
  public Class: Array<any> | any;
  public Property: Array<any> | any;

  ngOnInit() {
    this.brapciService.api_post('rdf').subscribe((res) => {
      this.rdf = res;
      this.Class = this.rdf['Class']
      this.Property = this.rdf['Property'];
    });
  }
}
