import { Injectable } from "@angular/core";
import { AnneeService } from "../annee.service";
import { Observable } from "rxjs";
import { AnneeModel } from "../../models/annee.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class AnneeServiceImpl implements AnneeService{
    private apiUrl=`${environment.APIURL}/annee`;
    constructor(private http:HttpClient){}

    findAll(): Observable<RestResponse<AnneeModel[]>> {
        return this.http.get<RestResponse<AnneeModel[]>>(this.apiUrl);
    }

}
