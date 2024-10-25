import { Observable } from "rxjs";
import { ListeModel } from "../models/liste.model";
import { RestResponse } from "../models/rest.response";

export interface ListeService{
    findAll(user:number, page:number, keyword:string, annee:number, ecole:number):Observable<RestResponse<ListeModel[]>>;
    findById(liste:number): Observable<RestResponse<ListeModel>>;
    reDoListe(liste:number):Observable<any>;
    modifListe(liste:number, keyword:string):Observable<any>;
}
