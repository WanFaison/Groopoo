import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { EtageModel } from "../models/etage.model";

export interface EtageService{
    findAllByEcole(ecole:number):Observable<RestResponse<EtageModel[]>>;    
    findAllPg(page:number, keyword:string, ecole:number):Observable<RestResponse<EtageModel[]>>;
    addEtage(data:any):Observable<any>;
    modifEtage(etage:number, keyword:string):Observable<any>;
}