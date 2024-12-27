import { Observable } from "rxjs";
import { ListeModel } from "../models/liste.model";
import { RequestResponse, RestResponse } from "../models/rest.response";

export interface ListeService{
    findAll(page:number, keyword:string, annee:number, ecole:number, archived:number):Observable<RestResponse<ListeModel[]>>;
    findById(liste:number): Observable<RestResponse<ListeModel>>;
    reDoListe(liste:number):Observable<any>;
    modifListe(liste:number, motif:string, keyword:string):Observable<any>;
    setNotes(data:any):Observable<any>;
    getTemplate(state:number):Observable<any>;
    importList(data:any):Observable<any>;
    deleteListe(liste:number):Observable<RequestResponse>;
    transferListe(liste:number, ecole:number):Observable<RequestResponse>;
}
