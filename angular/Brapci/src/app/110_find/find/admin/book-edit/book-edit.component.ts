import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { VitrineLivrosService } from 'src/app/100_brapci_livros/service/vitrine-livros.service';

@Component({
  selector: 'app-book-edit',
  templateUrl: './book-edit.component.html',
  styleUrls: ['./book-edit.component.css']
})
export class BookEditComponent {
  constructor(
    private vitrineLivrosService: VitrineLivrosService,
    private route: ActivatedRoute
  ) { }

  public id = 0

  ngOnInit()
    {
      const id = this.route.snapshot.paramMap.get('id')
      console.log("=="+id)
    }
}
