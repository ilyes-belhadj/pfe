import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { OffreEmploiService } from './offre-emploi.service';

describe('OffreEmploiService', () => {
  let service: OffreEmploiService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [OffreEmploiService]
    });
    service = TestBed.inject(OffreEmploiService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it("devrait récupérer toutes les offres d'emploi", () => {
    const mockOffres = [{ id: 1, intitule: 'Développeur' }, { id: 2, intitule: 'Designer' }];
    service.getAll().subscribe(offres => {
      expect(offres).toEqual(mockOffres);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/offres-emploi');
    expect(req.request.method).toBe('GET');
    req.flush(mockOffres);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 