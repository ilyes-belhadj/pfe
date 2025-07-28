import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { EvaluationService } from './evaluation.service';

describe('EvaluationService', () => {
  let service: EvaluationService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [EvaluationService]
    });
    service = TestBed.inject(EvaluationService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer toutes les évaluations', () => {
    const mockEvaluations = [{ id: 1, nom: 'Annuel' }, { id: 2, nom: 'Semestriel' }];
    service.getAll().subscribe(evaluations => {
      expect(evaluations).toEqual(mockEvaluations);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/evaluations');
    expect(req.request.method).toBe('GET');
    req.flush(mockEvaluations);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 