import { Injectable } from "@angular/core";
import { FiliereService } from "../filiere.service";
import { Observable } from "rxjs";
import { FiliereModel } from "../../models/filiere.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class FiliereServiceImpl implements FiliereService{
    private apiUrl=`${environment.APIURL}/filiere`;
    constructor(private http:HttpClient){}
    
    findAll(ecole: number =0): Observable<RestResponse<FiliereModel[]>> {
        return this.http.get<RestResponse<FiliereModel[]>>(`${this.apiUrl}?ecole=${ecole}`);
    }

}
