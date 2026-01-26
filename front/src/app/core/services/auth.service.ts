import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { AuthResponse, RestResponse } from '../models/rest.response';
import { LogUser } from '../models/user.model';

export interface AuthService {
  login(username: string, password: string): Observable<AuthResponse>;
  logout(): void;
  isLoggedIn(): boolean;
  getToken(): any;
  getUser(): LogUser;
  setUser(loggedUser: any): void;
}
