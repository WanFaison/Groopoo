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
    private apiUrl2=`${environment.APIURL}/liste-find`;
    private apiUrlRedo=`${environment.APIURL}/recreate-groupe`;
    private apiUrlModif=`${environment.APIURL}/liste-modif`;
    private today:Date = new Date();
    // private todayString:string ='';
    // private testString:string='';
    constructor(private http:HttpClient) { 
        // this.todayString = this.today.toISOString().slice(0,10);
        // this.testString = new Date('2022-02-12').toISOString().slice(0,10);
    }
    modifListe(liste: number, keyword:string=''): Observable<any> {
        return this.http.get<any>(`${this.apiUrlModif}?liste=${liste}&keyword=${encodeURIComponent(keyword)}`);
    }

    reDoListe(liste: number): Observable<any> {
        return this.http.get<any>(`${this.apiUrlRedo}?liste=${liste}`);
    }

    findAll(user:number, page:number=0, keyword:string='', annee:number=0, ecole:number=0): Observable<RestResponse<ListeModel[]>> {
        return this.http.get<RestResponse<ListeModel[]>>(`${this.apiUrl}?user=${user}&page=${page}&keyword=${keyword}&annee=${annee}&ecole=${ecole}`);
    }

    findById(liste:number): Observable<RestResponse<ListeModel>> {
        return this.http.get<RestResponse<ListeModel>>(`${this.apiUrl2}?liste=${liste}`);
    }

}
