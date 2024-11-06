import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpResponse } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment.development';
import { ReturnResponse } from '../models/return.model';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = `${environment.APIURL}/create-groupe`;
  private apiUrlXls = `${environment.APIURL}/liste-export`;
  private apiUrlPdf = `${environment.APIURL}/liste-export-pdf`;
  private apiUrlNewUser = `${environment.APIURL}/add-user`;

  constructor(private http: HttpClient) {}

  sendJsonData(data: any): Observable<any> {
    return this.http.post(this.apiUrl, data);
  }

  sendDataToBackend(data: ReturnResponse): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });
    return this.http.post(`${this.apiUrl}`, data, {headers});
  }

  sendNewUserToBack(data:any): Observable<any>{
    const headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });
    return this.http.post(`${this.apiUrlNewUser}`, data, {headers});
  }

  sendModifUserToBack(id:number, data:any): Observable<any>{
    const headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });
    return this.http.post(`${environment.APIURL}/modif-user/${id}`, data, {headers});
  }

  getExcelSheet(liste:number, motif:string = ''): Observable<any> {
    return this.http.get(`${this.apiUrlXls}?liste=${liste}&motif=${motif}`, { responseType: 'blob' });
  }

  getPdf(liste: number, options?: { observe: 'response', responseType: 'blob' }): Observable<any> {
    return this.http.get<HttpResponse<Blob>>(`${this.apiUrlPdf}?liste=${liste}`, {
      ...options,
      observe: 'response',
      responseType: 'blob' as 'json'
    });
  }
}
