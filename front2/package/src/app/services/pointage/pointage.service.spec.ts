import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { PointageService } from './pointage.service';

describe('PointageService', () => {
  let service: PointageService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [PointageService]
    });
    service = TestBed.inject(PointageService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer tous les pointages', () => {
    const mockPointages = [{ id: 1, nom: 'Entrée' }, { id: 2, nom: 'Sortie' }];
    service.getAll().subscribe((res: any) => {
      console.log(res); // ← Pour vérifier la structure dans la console
      this.pointages = res.data; // ← Utilise la clé data
    });
    const req = httpMock.expectOne('http://localhost:8000/api/pointages');
    expect(req.request.method).toBe('GET');
    req.flush({ data: mockPointages }); // <-- enveloppe dans un objet data
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 