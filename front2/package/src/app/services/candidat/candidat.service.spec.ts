import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { CandidatService } from './candidat.service';

describe('CandidatService', () => {
  let service: CandidatService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [CandidatService]
    });
    service = TestBed.inject(CandidatService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer tous les candidats', () => {
    const mockCandidats = [{ id: 1, nom: 'Alice' }, { id: 2, nom: 'Bob' }];
    service.getAll().subscribe(candidats => {
      expect(candidats).toEqual(mockCandidats);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/candidats');
    expect(req.request.method).toBe('GET');
    req.flush(mockCandidats);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 