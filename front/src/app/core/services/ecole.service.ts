import { Observable } from "rxjs";
import { AnneeModel } from "../models/annee.model";
import { RestResponse } from "../models/rest.response";
import { EcoleModel } from "../models/ecole.model";

export interface EcoleService{
    findAll():Observable<RestResponse<EcoleModel[]>>;
    findAllPg(page:number, keyword:string): Observable<RestResponse<EcoleModel[]>>;
    modifEcole(ecole:number):Observable<any>;
    addEcole(data:any): Observable<any>
    findById(ecole:number): Observable<RestResponse<EcoleModel>>;
}
