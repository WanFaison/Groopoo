import { Observable } from "rxjs";
import { GroupeModel } from "../models/groupe.model";
import { RestResponse } from "../models/rest.response";

export interface GroupeService{
    findAll(page:number, annee:number):Observable<RestResponse<GroupeModel[]>>;
}
