import { Observable } from "rxjs";
import { GroupeJourModel, GroupeModel, GroupeReqModel } from "../models/groupe.model";
import { RestResponse } from "../models/rest.response";

export interface GroupeService{
    findAll(liste:number, page:number, limit:number):Observable<RestResponse<GroupeModel[]>>;
    findAllReq(liste:number):Observable<RestResponse<GroupeReqModel[]>>
    findByJour(jour:number, page:number, limit:number, groupe:number):Observable<RestResponse<GroupeJourModel[]>>
    getSalleSheet(liste:number, motif:string):Observable<any>
}
