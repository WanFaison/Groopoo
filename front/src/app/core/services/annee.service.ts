import { Observable } from "rxjs";
import { AnneeModel } from "../models/annee.model";
import { RestResponse } from "../models/rest.response";

export interface AnneeService{
    findAll():Observable<RestResponse<AnneeModel[]>>;
}
