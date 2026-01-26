import { Injectable } from "@angular/core";
import { ThemeService } from "../theme.service";
import { Observable } from "rxjs";
import { RestResponse } from "../../models/rest.response";
import { ThemeModel } from "../../models/theme.model";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class ThemeServiceImpl implements ThemeService{
    constructor(private http:HttpClient){}

    findByListe(liste: number): Observable<RestResponse<ThemeModel[]>> {
        return this.http.get<RestResponse<ThemeModel[]>>(`${environment.APIURL}/get-theme?liste=${liste}`);
    }
    assignThemes(data: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/assign-theme`, data, {headers});
    }

    findAll(): Observable<RestResponse<ThemeModel[]>> {
        return this.http.get<RestResponse<ThemeModel[]>>(`${environment.APIURL}/theme-liste`);
    }
    findAllPg(page: number, keyword: string, limit: number=10): Observable<RestResponse<ThemeModel[]>> {
        return this.http.get<RestResponse<ThemeModel[]>>(`${environment.APIURL}/liste-theme?page=${page}&keyword=${keyword}&limit=${limit}`);
    }
    modifTheme(theme: number, keyword:string=''): Observable<any> {
        return this.http.get<any>(`${environment.APIURL}/theme-modif?theme=${theme}&keyword=${encodeURIComponent(keyword)}`);
    }
    addTheme(data: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/add-theme`, data, {headers});
    }

}
