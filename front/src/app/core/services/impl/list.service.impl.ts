import { Observable } from "rxjs";
import { ListeModel } from "../../models/liste.model";
import { RestResponse } from "../../models/rest.response";
import { ListeService } from "../list.service";
import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class ListeServiceImpl implements ListeService{
    private apiUrl=`${environment.APIURL}/liste`;
    private today:Date = new Date();
    // private todayString:string ='';
    // private testString:string='';
    constructor(private http:HttpClient) { 
        // this.todayString = this.today.toISOString().slice(0,10);
        // this.testString = new Date('2022-02-12').toISOString().slice(0,10);
    }

    findAll(page:number=0, keyword:string='', annee:number=0): Observable<RestResponse<ListeModel[]>> {
        return this.http.get<RestResponse<ListeModel[]>>(`${this.apiUrl}?page=${page}&keyword=${keyword}&annee=${annee}`);
    }

}
