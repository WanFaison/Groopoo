import { TestBed } from '@angular/core/testing';

import { AuthService } from '../auth.service';
import { AuthServiceImpl } from '../impl/auth.service.impl';

describe('AuthService', () => {
  let service: AuthService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AuthServiceImpl);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
