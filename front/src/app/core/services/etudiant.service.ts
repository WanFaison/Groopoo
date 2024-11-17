import { Observable } from "rxjs";
import { RequestResponse, RestResponse } from "../models/rest.response";
import { EtudiantCreate, EtudiantCreateXlsx, EtudiantModel } from "../models/etudiant.model";

export interface EtudiantService{
    findByListe(liste:number):Observable<RestResponse<EtudiantCreateXlsx[]>>;
    findById(etudiant:number):Observable<RestResponse<EtudiantModel>>;
    transferEtudiant(etudiant:number, groupe:number):Observable<RequestResponse>;
    deleteEtudiant(etudiant:number):Observable<RequestResponse>;
}