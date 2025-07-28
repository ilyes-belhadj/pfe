import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { ProjectService } from './project.service';

describe('ProjectService', () => {
  let service: ProjectService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [ProjectService]
    });
    service = TestBed.inject(ProjectService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer tous les projets', () => {
    const mockProjects = [{ id: 1, nom: 'CRM' }, { id: 2, nom: 'ERP' }];
    service.getAll().subscribe(projects => {
      expect(projects).toEqual(mockProjects);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/projects');
    expect(req.request.method).toBe('GET');
    req.flush(mockProjects);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 