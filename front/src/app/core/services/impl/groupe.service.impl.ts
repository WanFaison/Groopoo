import { Injectable } from "@angular/core";
import { GroupeService } from "../groupe.service";
import { Observable } from "rxjs";
import { GroupeJourModel, GroupeModel, GroupeReqModel } from "../../models/groupe.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class GroupeServiceImpl implements GroupeService{
    private apiUrl=`${environment.APIURL}/liste-groupe`;
    constructor(private http:HttpClient){}

    findByJour(jour: number, page: number=0, limit: number=1, groupe:number=0): Observable<RestResponse<GroupeJourModel[]>> {
        return this.http.get<RestResponse<GroupeJourModel[]>>(`${environment.APIURL}/etd-groupe?jour=${jour}&page=${page}&limit=${limit}&groupe=${groupe}`);
    }

    findAllReq(liste: number): Observable<RestResponse<GroupeReqModel[]>> {
        return this.http.get<RestResponse<GroupeReqModel[]>>(`${environment.APIURL}/all-groupe?liste=${liste}`);
    }

    findAll(liste:number, page: number=0, limit:number =10): Observable<RestResponse<GroupeModel[]>> {
        return this.http.get<RestResponse<GroupeModel[]>>(`${this.apiUrl}?liste=${liste}&page=${page}&limit=${limit}`)
    }

}
