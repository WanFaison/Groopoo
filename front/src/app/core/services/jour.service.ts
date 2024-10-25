import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { JourModel } from "../models/jour.model";

export interface JourService{
    findAllListe(liste:any):Observable<RestResponse<JourModel[]>>;
    addJour(liste:any, date:any):Observable<any>;
    modifJr(jour:any):Observable<any>;
}