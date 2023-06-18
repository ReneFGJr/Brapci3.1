import { UIuser } from './../interface/UIusers';
import { CanActivateFn } from '@angular/router';
import { UIuser } from '../interface/UIusers';

export const guardOauthGuard implemente CanActivateFn {
    construtor(
    private _UIuser: UIuser,
    private router: Route) {}

    canActivate()
      {
        return true;
      }

  return false;
};
