import { Injectable } from "@angular/core";
import { JuryService } from "../jury.service";
import { Observable } from "rxjs";
import { JuryModel } from "../../models/jury.model";
import { RestResponse } from "../../models/rest.response";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class JuryServiceImpl implements JuryService{
    constructor(private http:HttpClient){}

    getFinalJurySheet(liste: number): Observable<any> {
        return this.http.get(`${environment.APIURL}/final-export?liste=${liste}`, { responseType: 'blob' });
    }

    getJurySheet(liste: number, motif: string =''): Observable<any> {
        return this.http.get(`${environment.APIURL}/coach-export?liste=${liste}&motif=${motif}`, { responseType: 'blob' });
    }

    finalJury(liste: number): Observable<RestResponse<JuryModel[]>> {
        return this.http.get<RestResponse<JuryModel[]>>(`${environment.APIURL}/final-jury?liste=${liste}`);
    }

    removeCoach(coach: number, jury: number): Observable<any> {
        return this.http.get<any>(`${environment.APIURL}/remove-coach?coach=${coach}&jury=${jury}`);
    }

    findAllButOne(liste: number, coach: number): Observable<RestResponse<JuryModel[]>> {
        return this.http.get<RestResponse<JuryModel[]>>(`${environment.APIURL}/all-jury?coach=${coach}&liste=${liste}`);
    }

    findAllPg(page: number=0, liste: number=0, limit: number=0, keyword:string=''): Observable<RestResponse<JuryModel[]>> {
        return this.http.get<RestResponse<JuryModel[]>>(`${environment.APIURL}/liste-jury?page=${page}&limit=${limit}&keyword=${keyword}&liste=${liste}`);
    }
    
}