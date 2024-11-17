import { Observable } from "rxjs";
import { ListeModel } from "../../models/liste.model";
import { RequestResponse, RestResponse } from "../../models/rest.response";
import { ListeService } from "../list.service";
import { Injectable } from "@angular/core";
import { HttpClient, HttpHeaders } from "@angular/common/http";
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

    deleteListe(liste: number): Observable<RequestResponse> {
        return this.http.get<RequestResponse>(`${environment.APIURL}/liste-delete?liste=${liste}`);
    }

    importList(data: any): Observable<any> {
        const headers = new HttpHeaders({
            'Content-Type': 'application/json'
        });
        return this.http.post(`${environment.APIURL}/create-groupe-import`, data, {headers});
    }

    getTemplate(state:number): Observable<any> {
        //state == 0/null pour le template d'etudiants a utiliser pour creer les listes
        //state == 1 pour le template des groupes de liste passee a importer
        if(state == 1){
            return this.http.get(`${environment.APIURL}/template-import`, { responseType: 'blob' });
        }
        return this.http.get(`${environment.APIURL}/template`, { responseType: 'blob' });
    }

    setNotes(data: any): Observable<any> {
        const headers = new HttpHeaders({
            'Content-Type': 'application/json'
        });
        return this.http.post(`${environment.APIURL}/notes`, data, {headers});
    }

    modifListe(liste: number, motif:string='archive', keyword:string=''): Observable<any> {
        return this.http.get<any>(`${this.apiUrlModif}?liste=${liste}&keyword=${encodeURIComponent(keyword)}&motif=${motif}`);
    }

    reDoListe(liste: number): Observable<any> {
        return this.http.get<any>(`${this.apiUrlRedo}?liste=${liste}`);
    }

    findAll(page:number=0, keyword:string='', annee:number=0, ecole:number=0, archived:number=0): Observable<RestResponse<ListeModel[]>> {
        return this.http.get<RestResponse<ListeModel[]>>(`${this.apiUrl}?&page=${page}&keyword=${keyword}&annee=${annee}&ecole=${ecole}&archived=${archived}`);
    }

    findById(liste:number): Observable<RestResponse<ListeModel>> {
        return this.http.get<RestResponse<ListeModel>>(`${this.apiUrl2}?liste=${liste}`);
    }

}
