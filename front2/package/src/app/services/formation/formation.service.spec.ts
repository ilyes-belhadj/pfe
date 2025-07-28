import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { FormationService } from './formation.service';

describe('FormationService', () => {
  let service: FormationService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [FormationService]
    });
    service = TestBed.inject(FormationService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer toutes les formations', () => {
    const mockFormations = [{ id: 1, nom: 'Angular' }, { id: 2, nom: 'Laravel' }];
    service.getAll().subscribe(formations => {
      expect(formations).toEqual(mockFormations);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/formations');
    expect(req.request.method).toBe('GET');
    req.flush(mockFormations);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 