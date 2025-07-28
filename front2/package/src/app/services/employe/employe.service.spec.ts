import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { EmployeService } from './employe.service';

describe('EmployeService', () => {
  let service: EmployeService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [EmployeService]
    });
    service = TestBed.inject(EmployeService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer tous les employés', () => {
    const mockEmployes = [{ id: 1, nom: 'Jean' }, { id: 2, nom: 'Marie' }];
    service.getAll().subscribe(employes => {
      expect(employes).toEqual(mockEmployes);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/employes');
    expect(req.request.method).toBe('GET');
    req.flush(mockEmployes);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 