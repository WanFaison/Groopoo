import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { NiveauModel } from "../models/niveau.model";

export interface NiveauService{
    findAll():Observable<RestResponse<NiveauModel[]>>;
}
