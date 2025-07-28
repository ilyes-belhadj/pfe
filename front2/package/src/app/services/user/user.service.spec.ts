import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { UserService } from './user.service';

describe('UserService', () => {
  let service: UserService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [UserService]
    });
    service = TestBed.inject(UserService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  it('devrait récupérer tous les utilisateurs', () => {
    const mockUsers = [{ id: 1, nom: 'Alice' }, { id: 2, nom: 'Bob' }];
    service.getAll().subscribe(users => {
      expect(users).toEqual(mockUsers);
    });
    const req = httpMock.expectOne('http://localhost:8000/api/users');
    expect(req.request.method).toBe('GET');
    req.flush(mockUsers);
  });

  afterEach(() => {
    httpMock.verify();
  });
}); 