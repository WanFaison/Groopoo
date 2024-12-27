import { Injectable } from "@angular/core";
import { EtudiantService } from "../etudiant.service";
import { Observable, retry } from "rxjs";
import { EtudiantCreate, EtudiantCreateXlsx, EtudiantModel } from "../../models/etudiant.model";
import { RequestResponse, RestResponse } from "../../models/rest.response";
import { environment } from "../../../../environments/environment.development";
import { HttpClient, HttpHeaders } from "@angular/common/http";

@Injectable({
    providedIn: 'root'
})

export class EtudiantServiceImpl implements EtudiantService{
    private apiUrl=`${environment.APIURL}/liste-etudiant`;
    constructor(private http:HttpClient){}

    exportEtdListe(data: any): Observable<any> {
        return this.http.post(`${environment.APIURL}/etudiant-export`, {etudiants: data}, { responseType: 'blob' });
    }

    addEtudiantToListe(data: any, groupe: number): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/etudiant-to-liste`, {etdForm: data, groupe: groupe}, {headers});
    }

    deleteEtudiant(etudiant: number): Observable<RequestResponse> {
        return this.http.get<RequestResponse>(`${environment.APIURL}/etudiant-delete?etudiant=${etudiant}`);
    }
    
    transferEtudiant(etudiant: number, groupe: number): Observable<RequestResponse> {
        return this.http.get<RequestResponse>(`${environment.APIURL}/etudiant-transfer?etudiant=${etudiant}&groupe=${groupe}`);
    }

    findById(etudiant: number): Observable<RestResponse<EtudiantModel>> {
        return this.http.get<RestResponse<EtudiantModel>>(`${environment.APIURL}/etudiant-find?etudiant=${etudiant}`);
    }

    findByListe(liste: number): Observable<RestResponse<EtudiantCreateXlsx[]>> {
        return this.http.get<RestResponse<EtudiantCreateXlsx[]>>(`${this.apiUrl}?liste=${liste}`);
    }

}
