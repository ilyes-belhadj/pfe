import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { RoleService } from './role.service';

describe('RoleService', () => {
  let service: RoleService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [RoleService]
    });
    service = TestBed.inject(RoleService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer tous les rôles', () => {
    const mockRoles = [{ id: 1, nom: 'Admin' }, { id: 2, nom: 'User' }];
    service.getAll().subscribe(roles => {
      expect(roles).toEqual(mockRoles);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/roles');
    expect(req.request.method).toBe('GET');
    req.flush(mockRoles);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 