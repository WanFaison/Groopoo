import { Injectable } from "@angular/core";
import { NiveauModel } from "../../models/niveau.model";
import { NiveauService } from "../niveau.service";
import { Observable } from "rxjs";
import { RestResponse } from "../../models/rest.response";
import { environment } from "../../../../environments/environment.development";
import { HttpClient } from "@angular/common/http";

@Injectable({
    providedIn: 'root'
})

export class NiveauServiceImpl implements NiveauService{
    private apiUrl=`${environment.APIURL}/niveau`;
    constructor(private http:HttpClient){}

    findAll(): Observable<RestResponse<NiveauModel[]>> {
        return this.http.get<RestResponse<NiveauModel[]>>(this.apiUrl);
    }
    
}
