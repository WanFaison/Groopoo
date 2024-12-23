import { Injectable } from "@angular/core";
import { UserService } from "../user.service";
import { Observable } from "rxjs";
import { RestResponse } from "../../models/rest.response";
import { UserModel } from "../../models/user.model";
import { HttpClient } from "@angular/common/http";
import { environment } from "../../../../environments/environment.development";

@Injectable({
    providedIn: 'root'
})

export class UserServiceImpl implements UserService{
    private apiUrlPg=`${environment.APIURL}/lister-users`;
    private apiUrlModif=`${environment.APIURL}/user-modif`;
    constructor(private http:HttpClient){}

    findAllArchived(page: number, keyword: string, ecole: number): Observable<RestResponse<UserModel[]>> {
        return this.http.get<RestResponse<UserModel[]>>(`${this.apiUrlPg}?page=${page}&keyword=${keyword}&ecole=${ecole}&arch=${true}`);
    }

    modifUser(user: number, motif:number = 0): Observable<any> {
        return this.http.get<any>(`${this.apiUrlModif}?user=${user}&motif=${motif}`);
    }

    findAllPg(page: number=0, keyword: string='', ecole: number=0): Observable<RestResponse<UserModel[]>> {
        return this.http.get<RestResponse<UserModel[]>>(`${this.apiUrlPg}?page=${page}&keyword=${keyword}&ecole=${ecole}`);
    }

}
