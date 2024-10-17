import { HttpInterceptor, HttpRequest, HttpHandler, HttpInterceptorFn } from "@angular/common/http";
import { inject, Injectable } from "@angular/core";
import { AuthService } from "../auth.service";
import { AuthServiceImpl } from "./auth.service.impl";

export const authInterceptor: HttpInterceptorFn = (req, next) => {
    const authService = inject(AuthServiceImpl);
    const token = authService.getToken();
    //console.log(token);
    if (token) {
      const clonedRequest = req.clone({
        headers: req.headers.set('Authorization', `Bearer ${token}`)
      });
      return next(clonedRequest);
    }
  
    return next(req);
};