import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { CandidatureService } from './candidature.service';

describe('CandidatureService', () => {
  let service: CandidatureService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [CandidatureService]
    });
    service = TestBed.inject(CandidatureService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer toutes les candidatures', () => {
    const mockCandidatures = [{ id: 1, nom: 'Stage' }, { id: 2, nom: 'CDI' }];
    service.getAll().subscribe(candidatures => {
      expect(candidatures).toEqual(mockCandidatures);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/candidatures');
    expect(req.request.method).toBe('GET');
    req.flush(mockCandidatures);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 