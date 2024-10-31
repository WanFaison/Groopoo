import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { catchError, throwError } from 'rxjs';
import { AuthServiceImpl } from '../services/impl/auth.service.impl';

export const expiredTokenInterceptor: HttpInterceptorFn = (req, next) => {
    const router = inject(Router);
    const authService = inject(AuthServiceImpl);
  
    return next(req).pipe(
      catchError((error: HttpErrorResponse) => {
        if (error.status === 401 && error.error.message === 'Expired JWT Token') {
          authService.logout();
          router.navigate(['/authentification/login'])
        }
        return throwError(() => error);
      })
    );
  };