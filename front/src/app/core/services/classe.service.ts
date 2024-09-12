import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { NiveauModel } from "../models/niveau.model";
import { FiliereModel } from "../models/filiere.model";
import { ClasseModel } from "../models/classe.model";

export interface ClasseService{
    findAll(ecole:number):Observable<RestResponse<ClasseModel[]>>;
}
