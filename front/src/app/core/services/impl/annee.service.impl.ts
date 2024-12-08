import { Injectable } from "@angular/core";
import { AnneeService } from "../annee.service";
import { Observable } from "rxjs";
import { AnneeModel } from "../../models/annee.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class AnneeServiceImpl implements AnneeService{
    private apiUrl=`${environment.APIURL}/annee`;
    private apiUrlPg=`${environment.APIURL}/liste-annee`;
    private apiUrlAdd=`${environment.APIURL}/add-annee`;
    private apiUrlModif=`${environment.APIURL}/annee-modif`;
    constructor(private http:HttpClient){}

    modifAnnee(annee: number, keyword:string=''): Observable<any> {
        return this.http.get<any>(`${this.apiUrlModif}?annee=${annee}&keyword=${encodeURIComponent(keyword)}`);
    }

    getAddUrl(){
        return this.apiUrlAdd;
    }

    addAnnee(data:any): Observable<any>{
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${this.apiUrlAdd}`, data, {headers});
    }

    findAll(): Observable<RestResponse<AnneeModel[]>> {
        return this.http.get<RestResponse<AnneeModel[]>>(this.apiUrl);
    }

    findAllPg(page:number=0, keyword:string=''): Observable<RestResponse<AnneeModel[]>>
    {
        return this.http.get<RestResponse<AnneeModel[]>>(`${this.apiUrlPg}?page=${page}&keyword=${keyword}`)
    }

}
