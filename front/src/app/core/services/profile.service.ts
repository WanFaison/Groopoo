import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { ProfileModel } from "../models/profile.model";

export interface ProfileService{
    findAll():Observable<RestResponse<ProfileModel[]>>
}