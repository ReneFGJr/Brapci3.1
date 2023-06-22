import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { VitrineLivrosService } from '../../service/vitrine-livros.service';

@Component({
  selector: 'app-livro-view',
  templateUrl: './livro-view.component.html',
  styleUrls: ['./livro-view.component.css']
})
export class LivroViewComponent implements OnInit {

  constructor(
    private router: Router,
    private activatedRoute: ActivatedRoute,
    private vitrineLivrosService: VitrineLivrosService
  ) {}

  public itemBook:any = [];

  ngOnInit(): void {
    this.book;
  }

  get book() {
    const id = this.activatedRoute.snapshot.params['id'];
    console.log('===>' + id);

    this.vitrineLivrosService.getBook(id).subscribe(
      res=>{
        console.log(res);
        this.itemBook = res;
      }
    );
    return id;
  }
}
