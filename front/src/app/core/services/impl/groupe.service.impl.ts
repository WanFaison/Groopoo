import { Injectable } from "@angular/core";
import { GroupeService } from "../groupe.service";
import { Observable } from "rxjs";
import { GroupeModel } from "../../models/groupe.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class GroupeServiceImpl implements GroupeService{
    private apiUrl=`${environment.APIURL}/liste-groupe`;
    constructor(private http:HttpClient){}

    findAll(liste:number, page: number=0): Observable<RestResponse<GroupeModel[]>> {
        return this.http.get<RestResponse<GroupeModel[]>>(`${this.apiUrl}?liste=${liste}&page=${page}`)
    }

}
