import { ApplicationConfig, provideBrowserGlobalErrorListeners, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';

import { routes } from './app.routes';
import { provideClientHydration, withEventReplay } from '@angular/platform-browser';
import { provideHttpClient, withFetch, withInterceptors } from '@angular/common/http';
import { authInterceptor } from './core/interceptors/auth.interceptor';
import { AuthServiceImpl } from './core/services/impl/auth.service.impl';
import { loadingInterceptor } from './core/interceptors/loader.interceptor';
import { expiredTokenInterceptor } from './core/interceptors/expiredtoken.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [provideZoneChangeDetection({ eventCoalescing: true }), 
    provideRouter(routes), provideClientHydration(),
    provideHttpClient(withFetch(), 
    withInterceptors([authInterceptor, loadingInterceptor, expiredTokenInterceptor])),
    { provide: AuthServiceImpl, useClass: AuthServiceImpl }
  ]
};
