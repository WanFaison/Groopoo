import { Injectable } from "@angular/core";
import { FiliereService } from "../filiere.service";
import { Observable } from "rxjs";
import { FiliereModel } from "../../models/filiere.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";
import { ClasseService } from "../classe.service";
import { ClasseModel } from "../../models/classe.model";

@Injectable({
    providedIn: 'root'
})

export class ClasseServiceImpl implements ClasseService{
    private apiUrl=`${environment.APIURL}/classe`;
    constructor(private http:HttpClient){}
    
    findAll(ecole: number =0): Observable<RestResponse<ClasseModel[]>> {
        return this.http.get<RestResponse<ClasseModel[]>>(`${this.apiUrl}?ecole=${ecole}`);
    }

}
