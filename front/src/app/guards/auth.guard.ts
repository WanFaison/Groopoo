import { Injectable } from "@angular/core";
import { ActivatedRouteSnapshot, CanActivate, GuardResult, MaybeAsync, Router, RouterStateSnapshot } from "@angular/router";
import { AuthServiceImpl } from "../core/services/impl/auth.service.impl";

@Injectable({
    providedIn: 'root',
})

export class AuthGuard implements CanActivate {
    constructor(private authService: AuthServiceImpl, private router:Router) {}
    
    canActivate(): boolean{
        if (this.authService.isLoggedIn()) {
            return true;
          } else {
            this.router.navigateByUrl("/authentification/login");
            return false;
        }
    }
}
