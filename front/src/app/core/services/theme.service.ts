import { Observable } from "rxjs";
import { RestResponse } from "../models/rest.response";
import { ThemeModel } from "../models/theme.model";

export interface ThemeService{
    findAll():Observable<RestResponse<ThemeModel[]>>;
    findAllPg(page:number, keyword:string): Observable<RestResponse<ThemeModel[]>>
    modifTheme(theme:number, keyword:string):Observable<any>;
    addTheme(data:any): Observable<any>;
    findByListe(liste:number):Observable<RestResponse<ThemeModel[]>>;
    assignThemes(data:any):Observable<any>;
}