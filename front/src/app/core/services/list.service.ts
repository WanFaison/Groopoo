import { Observable } from "rxjs";
import { ListeModel } from "../models/liste.model";
import { RestResponse } from "../models/rest.response";

export interface ListeService{
    findAll(page:number,keyword:string):Observable<RestResponse<ListeModel[]>>;
}
