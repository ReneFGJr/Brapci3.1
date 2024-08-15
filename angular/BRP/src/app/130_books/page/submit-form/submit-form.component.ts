import { Observable } from 'rxjs';
import { BrapciService } from 'src/app/000_core/010_services/brapci.service';
import { Component } from '@angular/core';
import {
  FormGroup,
  Validators,
  FormBuilder,
  FormControl,
} from '@angular/forms';

/* Class */
import { BookSubmit } from '../../../000_class/book_submit';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ActivatedRoute, Router } from '@angular/router';
import { LocalStorageService } from 'src/app/001_auth/service/local-storage.service';

@Component({
  selector: 'app-book-submit-form',
  templateUrl: './submit-form.component.html',
  styleUrls: ['./submit-form.component.scss'],
})
export class BookSubmitFormComponent {
  submitted = false;
  public FormBook: FormGroup | any;

  public msg_title = 'Título do livro';
  public msg_isbn = 'Número do ISBN';
  public msg_submit = 'Enviar para análise';
  public msg_termSubmit = 'Termo de submissão';
  public msg_termAccept = 'Concordo com os termos';
  public msg_autor = 'Nome completo do autor';
  public msg_email = 'Informe seu e-mail do autor';
  public msg_licenca = 'Informe a licença da obra';
  public msg_check01 = 'Sou o autor ou organizador';
  private dt: Array<any> = [];
  public books: Array<any> | any;

  public logo_brapcilivros: string = 'assets/img/logo_brapci_livros_mini.png';

  public licence_image: string = '';
  public licence_text: string = '';

  licence_chage(img: string, desc: string) {
    this.licence_image = img;
    this.licence_text = desc;
  }

  public cc: Array<any> | any = [
    {
      name: 'CC-BY',
      img: 'https://www.sibi.usp.br/wp-content/uploads/2019/06/CC-BY-180x65.jpg',
      desc: 'Esta licença permite que outros distribuam, remixem, adaptem e criem a partir do seu trabalho, mesmo para fins comerciais, desde que lhe atribuam o devido crédito pela criação original. É a licença mais flexível de todas as licenças disponíveis. É recomendada para maximizar a disseminação e uso dos materiais licenciados. Particularmente com relação à licença CC-BY, é preciso destacar que, por ser o tipo de licença mais aberta tanto com relação às permissões e acessos, também é a licença que permite o uso dos conteúdos para fins comerciais. Isso significa que terceiros podem obter lucros com o trabalho alheio a qualquer momento, sem que o criador tenha qualquer controle.',
    },
    {
      name: 'CC-BY-SA',
      img: 'https://www.sibi.usp.br/wp-content/uploads/2019/06/CC-BY-SA-180x65.jpg',
      desc: 'Esta licença permite que outros remixem, adaptem e criem a partir do seu trabalho, mesmo para fins comerciais, desde que lhe atribuam o devido crédito e que licenciem as novas criações sob termos idênticos. Esta licença costuma ser comparada com as licenças de software livre e de código aberto “copyleft”. Todos os trabalhos novos baseados no seu terão a mesma licença. Portanto, quaisquer trabalhos derivados também permitirão o uso comercial. Esta é a licença usada pela Wikipédia e é recomendada para materiais que seriam beneficiados com a incorporação de conteúdos da Wikipédia e de outros projetos com licenciamento semelhante. ',
    },
    {
      name: 'CC-BY-NC',
      img: 'https://www.sibi.usp.br/wp-content/uploads/2019/06/CC-BY-ND-180x65.jpg',
      desc: 'Esta licença permite a redistribuição, comercial e não comercial, desde que o trabalho seja distribuído inalterado e no seu todo, com crédito atribuído ao autor.',
    },
    {
      name: 'CC-BY-NC-SA',
      img: 'https://www.sibi.usp.br/wp-content/uploads/2019/06/CC-BY-NC-SA-180x64.jpg',
      desc: 'Esta licença permite que outros remixem, adaptem e criem a partir do seu trabalho para fins não comerciais, desde que atribuam a você o devido crédito e que licenciem as novas criações sob termos idênticos.',
    },
    {
      name: 'CC-BY-ND',
      img: 'https://www.sibi.usp.br/wp-content/uploads/2019/06/CC-BY-ND-180x65.jpg',
      desc: 'Esta licença permite a redistribuição, comercial e não comercial, desde que o trabalho seja distribuído inalterado e no seu todo, com crédito atribuído ao autor.',
    },
    {
      name: 'CC-BY-NC-ND',
      img: 'https://www.sibi.usp.br/wp-content/uploads/2019/06/CC-BY-NC-ND-180x64.jpg',
      desc: 'Esta é a licenças mais restritiva, só permitindo que outros façam download dos seus trabalhos e os compartilhem desde que atribuam crédito a você, mas sem que possam alterá-los de nenhuma forma ou utilizá-los para fins comerciais.',
    },
    {
      name: 'CC0',
      img: 'http://www.sibi.usp.br/wp-content/uploads/2016/06/CC-O-public-Domain.png',
      desc: 'Esta licença CC0 permite aos cientistas, educadores, artistas e outros criadores de conteúdos a renunciar a qualquer direito reservado e, assim, colocá-los tão completamente quanto possível no domínio público, para que outros possam construir livremente em cima, melhorar e reutilizar as obras para quaisquer fins, sem restrições sob a legislação autoral ou banco de dados. ',
    },
  ];

  createForm2(bookSubmit: BookSubmit) {
    this.FormBook = this.formBuilder.group({
      id_b: new FormControl(0),
      b_autor: new FormControl('', [
        Validators.required,
        Validators.minLength(8),
      ]),
      b_email: new FormControl('', [
        Validators.required,
        Validators.minLength(8),
        Validators.pattern('^[a-z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}$'),
      ]),
      b_titulo: new FormControl('', [
        Validators.required,
        Validators.minLength(8),
      ]),
      b_isbn: new FormControl(''),
      b_licenca: new FormControl('', [Validators.required]),
      b_source: new FormControl(''),
      b_rdf: new FormControl(''),
      b_pdf: new FormControl('', [Validators.required]),
      b_user: new FormControl(''),
      b_termSubmit: new FormControl('', Validators.required),
      b_check_01: new FormControl('', Validators.required),
    });
  }

  constructor(
    private fb: FormBuilder,
    private brapciService: BrapciService,
    private localStorageService: LocalStorageService,
    private router: Router,
    private route: ActivatedRoute,
    private formBuilder: FormBuilder,
    private http: HttpClient
  ) {}

  file: Array<any> | any;
  property: string = '';
  type: string = '';
  ID: string = '';
  status: string = '';
  xClass: string = 'pdfBOOK';

  // On file Select
  onChange(event: any) {
    const file: File = event.target.files[0];

    if (file) {
      this.status = '';
      this.file = file;
    }
  }

  onUpload() {
    if (this.file) {
      const formData = new FormData();
      this.type = 'pdfBOOK';

      let url = this.brapciService.url + 'upload/' + this.type;
      //let url = 'http://brp/api/' + 'upload/' + this.type + '/' + this.ID

      formData.append('file', this.file, this.file.name);
      formData.append('property', this.property);

      const upload$ = this.http.post(url, formData);
      this.status = 'uploading';

    upload$.subscribe({
      next: (response: any) => {
        console.log('Upload bem-sucedido:', response);

        // Se a resposta for JSON, você pode acessá-la diretamente
        const jsonResponse = response; // Supondo que a resposta já seja um objeto JSON
        if (response.status == '200')
          {
            if (response.PID[1])
              {
                this.status = 'already';
                console.log("Já existe uma submissão com esse arquivo")
              } else {
                this.status = 'success';
                console.log('Arquivo novo');
              }
          }

        // Exemplo de como acessar uma propriedade do JSON
        if (jsonResponse.propertyName) {
          console.log('Propriedade:', jsonResponse.propertyName);
        }
      },
      error: (error: any) => {
        console.error('Erro no upload:', error);
        this.status = 'fail';
      },
      complete: () => {
        console.log('Requisição completa');
      },
    });
    }
  }

  createForm(bookSubmit: BookSubmit) {
    this.FormBook = this.formBuilder.group({
      id_b: new FormControl(0),
      b_autor: new FormControl(''),
      b_email: new FormControl(''),
      b_titulo: new FormControl(''),
      b_isbn: new FormControl(''),
      b_licenca: new FormControl(''),
      b_source: new FormControl(''),
      b_rdf: new FormControl(''),
      b_pdf: new FormControl(''),
      b_user: new FormControl(''),
      b_termSubmit: new FormControl(''),
      b_check_01: new FormControl(''),
    });
  }

  ngOnInit() {
    this.createForm(new BookSubmit());
    console.log(this.FormBook);
  }

  onSubmit() {
    //this.submitted = true;
    /*
    if (this.FormBook.status == 'VALID') {
      this.dt = this.FormBook.value;
      this.brapciService.api_post('book/submit', this.dt).subscribe((res) => {
        this.books = res;
      });
    }
    */
    console.log("SUBMIT")
  }

  addPDF(newItem: string) {
    this.FormBook.controls['b_pdf'].setValue(newItem);
    console.log('Botão addPDF');
  }
}
