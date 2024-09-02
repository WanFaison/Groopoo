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
    constructor(private http:HttpClient) { 
    }

    findAll(page:number=0, keyword:string=''): Observable<RestResponse<ListeModel[]>> {
        return this.http.get<RestResponse<ListeModel[]>>(`${this.apiUrl}?page=${page}&keyword=${keyword}`);
    }

}
