import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { SalleModel } from "../models/salle.model";

export interface SalleService{
    findAllPg(page:number, keyword:string, ecole:number):Observable<RestResponse<SalleModel[]>>;
    modifSalle(salle:number, keyword:string):Observable<any>;
    addSalle(data:any): Observable<any>;
}