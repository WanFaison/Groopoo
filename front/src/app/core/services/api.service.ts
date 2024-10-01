import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
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

  getExcelSheet(liste:number): Observable<any> {
    return this.http.get(`${this.apiUrlXls}?liste=${liste}`, { responseType: 'blob' });
  }

  getPdf(liste:number): Observable<any> {
    return this.http.get(`${this.apiUrlPdf}?liste=${liste}`, { responseType: 'blob', observe: 'response' });
  }
}
