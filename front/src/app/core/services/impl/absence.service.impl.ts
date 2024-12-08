import { Observable } from "rxjs";
import { AbsenceService } from "../absence.service";
import { Injectable } from "@angular/core";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class AbsenceImplService implements AbsenceService{
    constructor(private http: HttpClient) {}

    sendAbsences(data: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/mark-absence`, data, {headers});
    }

}
