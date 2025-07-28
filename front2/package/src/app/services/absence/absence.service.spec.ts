import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { AbsenceService } from './absence.service';

describe('AbsenceService', () => {
  let service: AbsenceService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [AbsenceService]
    });
    service = TestBed.inject(AbsenceService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer toutes les absences', () => {
    const mockAbsences = [{ id: 1, nom: 'Congé' }, { id: 2, nom: 'Maladie' }];
    service.getAll().subscribe(absences => {
      expect(absences).toEqual(mockAbsences);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/absences');
    expect(req.request.method).toBe('GET');
    req.flush(mockAbsences);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 