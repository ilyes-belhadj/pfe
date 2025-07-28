import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class EmployeService {
  private apiUrl = 'http://127.0.0.1:8000/api/auth/employes';

  constructor(private http: HttpClient) {}

  getAll(): Observable<any> {
    return this.http.get(this.apiUrl);
  }
  create(data: any) {
    return this.http.post(this.apiUrl, data);
  }
  update(id: number, data: any) {
    return this.http.put(`${this.apiUrl}/${id}`, data);
  }
  delete(id: number) {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
} 