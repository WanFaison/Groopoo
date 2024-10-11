import { Injectable } from "@angular/core";
import { ProfileService } from "../profile.service";
import { Observable } from "rxjs";
import { ProfileModel } from "../../models/profile.model";
import { RestResponse } from "../../models/rest.response";
import { environment } from "../../../../environments/environment.development";
import { HttpClient } from "@angular/common/http";

@Injectable({
    providedIn: 'root'
})

export class ProfileServiceImpl implements ProfileService{
    private apiUrl=`${environment.APIURL}/profile-liste`;
    constructor(private http:HttpClient){}

    findAll(): Observable<RestResponse<ProfileModel[]>> {
        return this.http.get<RestResponse<ProfileModel[]>>(this.apiUrl);
    }

}
