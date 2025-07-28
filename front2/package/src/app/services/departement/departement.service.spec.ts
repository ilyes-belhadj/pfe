import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { DepartementService } from './departement.service';

describe('DepartementService', () => {
  let service: DepartementService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [DepartementService]
    });
    service = TestBed.inject(DepartementService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer tous les départements', () => {
    const mockDepartements = [{ id: 1, nom: 'RH' }, { id: 2, nom: 'IT' }];
    service.getAll().subscribe(departements => {
      expect(departements).toEqual(mockDepartements);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/departements');
    expect(req.request.method).toBe('GET');
    req.flush(mockDepartements);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 