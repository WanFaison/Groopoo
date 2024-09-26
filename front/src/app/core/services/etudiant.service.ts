import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { EtudiantCreate, EtudiantCreateXlsx } from "../models/etudiant.model";

export interface EtudiantService{
    findByListe(liste:number):Observable<RestResponse<EtudiantCreateXlsx[]>>;
}