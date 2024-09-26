import { Injectable } from "@angular/core";
import { EtudiantService } from "../etudiant.service";
import { Observable } from "rxjs";
import { EtudiantCreate, EtudiantCreateXlsx } from "../../models/etudiant.model";
import { RestResponse } from "../../models/rest.response";
import { environment } from "../../../../environments/environment.development";
import { HttpClient } from "@angular/common/http";

@Injectable({
    providedIn: 'root'
})

export class EtudiantServiceImpl implements EtudiantService{
    private apiUrl=`${environment.APIURL}/liste-etudiant`;
    constructor(private http:HttpClient){}

    findByListe(liste: number): Observable<RestResponse<EtudiantCreateXlsx[]>> {
        return this.http.get<RestResponse<EtudiantCreateXlsx[]>>(`${this.apiUrl}?liste=${liste}`);
    }

}
