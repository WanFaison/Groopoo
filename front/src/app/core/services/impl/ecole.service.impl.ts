import { Injectable } from "@angular/core";
import { EcoleService } from "../ecole.service";
import { Observable } from "rxjs";
import { EcoleModel } from "../../models/ecole.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class EcoleServiceImpl implements EcoleService{
    private apiUrl=`${environment.APIURL}/ecole`;
    constructor(private http:HttpClient){}
    
    findAll(): Observable<RestResponse<EcoleModel[]>> {
        return this.http.get<RestResponse<EcoleModel[]>>(this.apiUrl);
    }

}
