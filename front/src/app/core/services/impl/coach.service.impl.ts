import { Injectable } from "@angular/core";
import { CoachService } from "../coach.service";
import { Observable } from "rxjs";
import { CoachModel } from "../../models/coach.model";
import { RequestResponse, RestResponse } from "../../models/rest.response";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class CoachServiceImpl implements CoachService{
    constructor(private http:HttpClient){}

    modifCoach(coach: number): Observable<any> {
        return this.http.get<any>(`${environment.APIURL}/coach-modif?coach=${coach}`);
    }

    findAllNotInListe(liste: number): Observable<RestResponse<CoachModel[]>> {
        return this.http.get<RestResponse<CoachModel[]>>(`${environment.APIURL}/coach-find-unaffected?liste=${liste}`);
    }

    transferCoach(coach: number, jury: number): Observable<RequestResponse> {
        return this.http.get<RequestResponse>(`${environment.APIURL}/coach-transfer?coach=${coach}&jury=${jury}`);
    }

    findById(coach: number): Observable<RestResponse<CoachModel>> {
        return this.http.get<RestResponse<CoachModel>>(`${environment.APIURL}/coach-find?coach=${coach}`);
    }

    assignCoaches(data: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/assign-coach`, data, {headers});
    }

    findByListe(liste: number): Observable<RestResponse<CoachModel[]>> {
        return this.http.get<RestResponse<CoachModel[]>>(`${environment.APIURL}/get-coach?liste=${liste}`);
    }

    addCoach(data: any): Observable<any> {
        const headers = new HttpHeaders(environment.JSONHeaders);
        return this.http.post(`${environment.APIURL}/add-coach`, data, {headers});
    }

    findAllPg(page: number, keyword: string='', ecole: number, liste:number=0): Observable<RestResponse<CoachModel[]>> {
        return this.http.get<RestResponse<CoachModel[]>>(`${environment.APIURL}/liste-coach?page=${page}&keyword=${keyword}&ecole=${ecole}&liste=${liste}`);
    }
    
}