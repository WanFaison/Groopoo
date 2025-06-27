import { Observable } from "rxjs";
import { RestResponse } from "../../models/rest.response";
import { SalleModel } from "../../models/salle.model";
import { SalleService } from "../salle.service";
import { Injectable } from "@angular/core";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class SalleServiceImpl implements SalleService{
    constructor(private http:HttpClient){}

    addSalle(data: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/add-salle`, data, {headers});
    }

    modifSalle(salle: number = 0, keyword: string=''): Observable<any> {
        return this.http.get<any>(`${environment.APIURL}/salle-modif?salle=${salle}&keyword=${encodeURIComponent(keyword)}`);
    }

    findByEcole(ecole: number=0):Observable<RestResponse<SalleModel[]>>{
        return this.http.get<RestResponse<SalleModel[]>>(`${environment.APIURL}/get-salle?ecole=${ecole}`)
    }

    findByList(liste: number=0):Observable<RestResponse<SalleModel[]>>{
        return this.http.get<RestResponse<SalleModel[]>>(`${environment.APIURL}/get-salle-active?liste=${liste}`)
    }

    findAllPg(page: number=0, keyword: string='', ecole: number=0): Observable<RestResponse<SalleModel[]>> {
        return this.http.get<RestResponse<SalleModel[]>>(`${environment.APIURL}/liste-salle?page=${page}&keyword=${keyword}&ecole=${ecole}`);
    }

}
