import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { AuthService } from 'src/app/000_core/010_services/orcid.service';

@Component({
  selector: 'app-callback-orcid',
  templateUrl: './orcid.component.html',
})
export class OrcidCallBackComponent {
  constructor(
    private route: ActivatedRoute,
    private authService: AuthService
  ) {}

  ngOnInit() {
    this.route.params.subscribe((params) => {
      const id = params['id'];
      if (id) {
        alert(id);
      }
    });

    this.route.queryParams.subscribe((params) => {
      const code = params['code'];
      if (code) {
        this.authService.getAccessToken(code).subscribe((response) => {
          console.log('Token de Acesso:', response);
          // Salve o token e redirecione conforme necessário
        });
      }
    });
  }
}
