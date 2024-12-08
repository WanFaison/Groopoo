import { Observable } from "rxjs";
import { EtageModel } from "../models/etage.model";
import { RequestResponse, RestResponse } from "../models/rest.response";
import { CoachModel } from "../models/coach.model";

export interface CoachService{
    findAllPg(page:number, keyword:string, ecole:number):Observable<RestResponse<CoachModel[]>>;
    addCoach(data:any):Observable<any>;
    findByListe(liste:number):Observable<RestResponse<CoachModel[]>>;
    assignCoaches(data:any):Observable<any>;
    findById(coach:number):Observable<RestResponse<CoachModel>>;
    transferCoach(coach:number, jury:number):Observable<RequestResponse>;
    findAllNotInListe(liste:number):Observable<RestResponse<CoachModel[]>>;
    modifCoach(coach:number):Observable<any>;
}