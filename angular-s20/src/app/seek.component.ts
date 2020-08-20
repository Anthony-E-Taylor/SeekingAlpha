import { Component, Input } from '@angular/core';
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import data from './seek.json';
interface File {
  file: string;
  date: string;
}


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})

export class SeekComponent  {


@Injectable({
  providedIn: 'root'
})
 constructor(private http: HttpClient) { }
  getUsers() {
    return this.http.get('http://localhost/seeking_alpha_dividend.php');
  } //this.http.post(URL + /${productId});
  getProduct(productId) {
    const params = new HttpParams().set('id', productId);
    return this.http.get('http://localhost/seeking_alpha_dividend.php/', { params });
  }


}
