import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { UserModel } from "../models/user.model";

export interface UserService{
    findAllPg(page:number, keyword:string, ecole:number):Observable<RestResponse<UserModel[]>>;
    modifUser(user:number, motif:number):Observable<any>;
    findAllArchived(page:number, keyword:string, ecole:number):Observable<RestResponse<UserModel[]>>;
}
