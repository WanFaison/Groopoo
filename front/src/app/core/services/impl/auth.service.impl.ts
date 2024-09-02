import { Injectable } from "@angular/core";
import { AuthService } from "../auth.service";
import { environment } from "../../../../environments/environment.development";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Observable } from "rxjs";
import { RestResponse } from "../../models/rest.response";

@Injectable({
    providedIn: 'root'
  })

export class AuthServiceImpl implements AuthService{
    private apiUrl=`${environment.APIURL}/login`;
    private isAuth = false;
    constructor(private http:HttpClient) { 
    }

    login(username: string, password: string): Observable<RestResponse<AuthService>> {
        const headers = new HttpHeaders({ 'Content-Type': 'application/json' });
        const body = { username, password };
    
        return this.http.post<any>(this.apiUrl, body, { headers });
    }

    logout(): void {
        this.isAuth = false;
    }
    
    setAuth(auth: boolean): void {
        this.isAuth = auth;
    }
    
    isLoggedIn(): boolean {
        return this.isAuth;
    }
}
