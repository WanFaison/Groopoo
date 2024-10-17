import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { AuthResponse, RestResponse } from '../models/rest.response';

export interface AuthService {
  login(username: string, password: string): Observable<AuthResponse>;
  logout(): void;
  isLoggedIn(): boolean;
  getToken(): any
}
