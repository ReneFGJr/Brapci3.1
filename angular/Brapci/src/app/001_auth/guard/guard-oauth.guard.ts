import { CanActivateFn } from '@angular/router';
import { UserService } from '../service/user.service';
import { from, fromEvent } from 'rxjs';

export const guardOauthGuard: CanActivateFn = (route, state) => {
  console.log(route)
  return check();
};

function check():boolean {
    console.log("CHECK POST")
  return true;
}
