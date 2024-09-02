import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { RestResponse } from '../models/rest.response';

export interface AuthService {
  login(username: string, password: string): Observable<RestResponse<AuthService>>;
  logout(): void;
  setAuth(auth: boolean): void;
  isLoggedIn(): boolean;
}
