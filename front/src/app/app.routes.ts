import { Routes } from '@angular/router';
import { LoginComponent } from './core/pages/login/login.component';
import { HomeComponent } from './core/pages/home/home.component';
import { MembresComponent } from './core/pages/membres/membres.component';
import { SelectlistComponent } from './core/pages/selectlist/selectlist.component';
import { AuthGuard } from './guards/auth.guard';
import { FormCritereComponent } from './core/pages/form-critere/form-critere.component';
import { GroupsComponent } from './core/pages/groups/groups.component';

export const routes: Routes = [
    {
        path:"authentification/login",
        component:LoginComponent
    },
    {
        path:"app/home",
        component:HomeComponent,
        // canActivate: [AuthGuard]
    },
    {
        path:"app/liste-membre",
        component:MembresComponent,
        //canActivate: [AuthGuard]
    },
    {
        path:"app/form-critere",
        component:FormCritereComponent,
        //canActivate: [AuthGuard]
    },
    {
        path:"app/view-groups",
        component:GroupsComponent,
        //canActivate: [AuthGuard]
    },
    {
        path:"app/liste-select",
        component:SelectlistComponent,
        //canActivate: [AuthGuard]
    },
    {
        path:"",
        redirectTo:"/authentification/login",
        pathMatch:'full'
    }
];
