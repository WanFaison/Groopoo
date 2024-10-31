import { Observable } from "rxjs";

export interface AbsenceService{
    sendAbsences(data:any):Observable<any>
}