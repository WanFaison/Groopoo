import { Observable } from "rxjs";
import { GroupeFinalModel, GroupeJourModel, GroupeModel, GroupeReqModel } from "../models/groupe.model";
import { RestResponse } from "../models/rest.response";

export interface GroupeService{
    findAll(liste:number, page:number, limit:number):Observable<RestResponse<GroupeModel[]>>;
    findAllReq(liste:number, coach:number):Observable<RestResponse<GroupeReqModel[]>>
    findByJour(jour:number, coach:number, page:number, limit:number, groupe:number):Observable<RestResponse<GroupeJourModel[]>>
    getSalleSheet(liste:number, motif:string):Observable<any>
    removeEmptyGroups(): Observable<any>
    findTop100(liste: number): Observable<RestResponse<GroupeFinalModel[]>>
    findTop2PerCoach(liste: number): Observable<RestResponse<GroupeFinalModel[]>>
}
