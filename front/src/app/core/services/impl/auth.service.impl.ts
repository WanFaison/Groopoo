import { Inject, Injectable, PLATFORM_ID } from "@angular/core";
import { AuthService } from "../auth.service";
import { environment } from "../../../../environments/environment.development";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Observable } from "rxjs";
import { AuthResponse, RestResponse } from "../../models/rest.response";
import { LogUser } from "../../models/user.model";
import { isPlatformBrowser } from "@angular/common";

@Injectable({
    providedIn: 'root'
  })

export class AuthServiceImpl implements AuthService{
    private apiUrl=`${environment.APIURL}/login`;
    private token:string = '';
    private logUser?:LogUser;
    constructor(private http:HttpClient, @Inject(PLATFORM_ID) private platformId: Object) { 
    }

    login(username: string, password: string): Observable<AuthResponse> {
        const headers = new HttpHeaders({ 'Content-Type': 'application/json' });
        const body = { username, password };
    
        return this.http.post<any>(this.apiUrl, body, { headers });
    }

    getToken() {
        return localStorage.getItem('jwtToken');
        //return this.token;
        //return sessionStorage.getItem('jwtToken')
    }
    setToken(tk:any){
        this.token = tk;
    }

    getUser(){
        const user = localStorage.getItem('logUser');
        if (user) {
            return JSON.parse(user) as LogUser;
        }
        return this.logUser;
    }
    setUser(loggedUser: any){
        this.logUser = loggedUser;
        localStorage.setItem('logUser', JSON.stringify(loggedUser));
    }

    logout(): void {
        this.token = '';
        if (typeof window !== 'undefined' && localStorage){
            localStorage.clear()
        }
        this.logUser = undefined
    }
    
    isLoggedIn(): boolean {
        return !!localStorage.getItem('jwtToken');
        //return this.isAuth;
    }
}
