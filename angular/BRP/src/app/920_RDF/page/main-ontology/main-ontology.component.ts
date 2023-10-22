import { Observable } from 'rxjs/internal/Observable';
import { Component } from '@angular/core';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';

@Component({
  selector: 'app-rdf-main-ontology',
  templateUrl: './main-ontology.component.html',
})
export class MainRdfOntologyComponent {
  constructor(public brapciService: BrapciService) {}
  public rdf:Array<any>|any

  ngOnInit()
    {
      this.brapciService.generic('rdf').subscribe((res) => {
        this.rdf = res;
      });
    }
}
