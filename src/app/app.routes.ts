import { Routes } from '@angular/router';
import { LoginComponent } from './core/pages/login/login.component';
import { HomeComponent } from './core/pages/home/home.component';
import { MembresComponent } from './core/pages/membres/membres.component';
import { SelectlistComponent } from './core/pages/selectlist/selectlist.component';

export const routes: Routes = [
    {
        path:"authentification/login",
        component:LoginComponent
    },
    {
        path:"app/home",
        component:HomeComponent
    },
    {
        path:"app/liste-membre",
        component:MembresComponent
    },
    {
        path:"app/liste-select",
        component:SelectlistComponent
    },
    {
        path:"",
        redirectTo:"/authentification/login",
        pathMatch:'full'
    }
];
