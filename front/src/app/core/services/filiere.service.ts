import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { NiveauModel } from "../models/niveau.model";
import { FiliereModel } from "../models/filiere.model";

export interface FiliereService{
    findAll(ecole:number):Observable<RestResponse<FiliereModel[]>>;
}
