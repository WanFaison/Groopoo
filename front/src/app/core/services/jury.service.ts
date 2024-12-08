import { Observable } from "rxjs";
import { JuryModel } from "../models/jury.model";
import { RestResponse } from "../models/rest.response";

export interface JuryService{
    findAllPg(page:number, liste:number, limit:number, keyword:string):Observable<RestResponse<JuryModel[]>>;
    findAllButOne(liste:number, coach:number):Observable<RestResponse<JuryModel[]>>;
}