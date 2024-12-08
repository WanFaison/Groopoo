import { Injectable } from "@angular/core";
import { EcoleService } from "../ecole.service";
import { Observable } from "rxjs";
import { EcoleModel } from "../../models/ecole.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class EcoleServiceImpl implements EcoleService{
    private apiUrl=`${environment.APIURL}/ecole`;
    private apiUrlPg=`${environment.APIURL}/liste-ecole`;
    private apiUrlAdd=`${environment.APIURL}/add-ecole`;
    private apiUrlModif=`${environment.APIURL}/ecole-modif`;
    constructor(private http:HttpClient){}

    findById(ecole: number): Observable<RestResponse<EcoleModel>> {
        return this.http.get<RestResponse<EcoleModel>>(`${environment.APIURL}/ecole-find?ecole=${ecole}`);
    }

    modifEcole(ecole: number, keyword:string=''): Observable<any> {
        return this.http.get<any>(`${this.apiUrlModif}?ecole=${ecole}&keyword=${encodeURIComponent(keyword)}`);
    }

    getAddUrl(){
        return this.apiUrlAdd;
    }

    addEcole(data:any): Observable<any>{
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${this.apiUrlAdd}`, data, {headers});
    }

    findAllPg(page: number, keyword: string): Observable<RestResponse<EcoleModel[]>> {
        return this.http.get<RestResponse<EcoleModel[]>>(`${this.apiUrlPg}?page=${page}&keyword=${keyword}`)
    }
    
    findAll(): Observable<RestResponse<EcoleModel[]>> {
        return this.http.get<RestResponse<EcoleModel[]>>(this.apiUrl);
    }

}
