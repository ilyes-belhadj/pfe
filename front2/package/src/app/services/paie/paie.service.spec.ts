import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { PaieService } from './paie.service';

describe('PaieService', () => {
  let service: PaieService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [PaieService]
    });
    service = TestBed.inject(PaieService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer toutes les paies', () => {
    const mockPaies = [{ id: 1, nom: 'Salaire Janvier' }, { id: 2, nom: 'Salaire Février' }];
    service.getAll().subscribe(paies => {
      expect(paies).toEqual(mockPaies);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/paies');
    expect(req.request.method).toBe('GET');
    req.flush(mockPaies);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 