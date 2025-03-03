import { Injectable } from "@angular/core";
import { JourService } from "../jour.service";
import { Observable } from "rxjs";
import { JourModel } from "../../models/jour.model";
import { FindRequestResponse, RestResponse } from "../../models/rest.response";
import { environment } from "../../../../environments/environment.development";
import { HttpClient, HttpHeaders } from "@angular/common/http";

@Injectable({
    providedIn: 'root'
})

export class JourServiceImpl implements JourService{
    private apiUrlFind=`${environment.APIURL}/lister-jour`;
    constructor(private http:HttpClient){}

    findJour(jour: any): Observable<FindRequestResponse<JourModel>> {
        return this.http.get<FindRequestResponse<JourModel>>(`${environment.APIURL}/find-jour?jour=${jour}`);
    }

    modifJr(jour: any): Observable<any> {
        return this.http.get<any>(`${environment.APIURL}/jour-modif?jour=${jour}`);
    }

    addJour(liste: any, date: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post<any>(`${environment.APIURL}/add-jour/${liste}`, date, {headers});
    }

    findAllListe(liste: any): Observable<RestResponse<JourModel[]>> {
        return this.http.get<RestResponse<JourModel[]>>(`${this.apiUrlFind}?liste=${liste}`);
    }
    
}
