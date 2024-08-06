import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { AuthService } from 'src/app/000_core/010_services/orcid.service';
import { UserService } from 'src/app/001_auth/service/user.service';

@Component({
  selector: 'app-callback-orcid',
  templateUrl: './orcid.component.html',
})
export class OrcidCallBackComponent {
  constructor(
    private route: ActivatedRoute,
    private authService: AuthService,
    private userService: UserService
  ) {}

  ngOnInit() {
    this.route.params.subscribe((params) => {
      const id = params['id'];
      if (id) {
        console.log("ID-oauth2:"+id)
        console.log(this.userService.loginOauthHttp(id));
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
