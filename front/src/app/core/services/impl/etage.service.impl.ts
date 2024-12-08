import { Injectable } from "@angular/core";
import { EtageService } from "../etage.service";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Observable } from "rxjs";
import { EtageModel } from "../../models/etage.model";
import { RestResponse } from "../../models/rest.response";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class EtageServiceImpl implements EtageService{
    constructor(private http:HttpClient){}

    modifEtage(etage: number=0, keyword: string=''): Observable<any> {
        return this.http.get<any>(`${environment.APIURL}/etage-modif?etage=${etage}&keyword=${encodeURIComponent(keyword)}`);
    }
    
    addEtage(data: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/add-etage`, data, {headers});
    }

    findAllPg(page: number=0, keyword: string='', ecole: number=0): Observable<RestResponse<EtageModel[]>> {
        return this.http.get<RestResponse<EtageModel[]>>(`${environment.APIURL}/liste-etage?page=${page}&keyword=${keyword}&ecole=${ecole}`);
    }

    findAllByEcole(ecole: number): Observable<RestResponse<EtageModel[]>> {
        return this.http.get<RestResponse<EtageModel[]>>(`${environment.APIURL}/all-etage?ecole=${ecole}`);
    }
}