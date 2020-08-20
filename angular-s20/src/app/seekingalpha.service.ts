import { Injectable } from '@angular/core';

import { HttpClient, HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';

import { SeekingAlpha } from './seekingalpha';

@Injectable({
  providedIn: 'root'
})
export class FileService {

  constructor( private http: HttpClient ) { }

  sendRequest(data: any ): Observable<any> {
    let params = "str=" + data.xmlfname;
    return this.http.get<any>('http://localhost/ngphp-get.php?'+params)
  }
}

