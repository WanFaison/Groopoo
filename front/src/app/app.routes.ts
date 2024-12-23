import { Routes } from '@angular/router';
import { LoginComponent } from './core/pages/login/login.component';
import { HomeComponent } from './core/pages/home/home.component';
import { MembresComponent } from './core/pages/membres/membres.component';
import { SelectlistComponent } from './core/pages/selectlist/selectlist.component';
import { AuthGuard } from './guards/auth.guard';
import { FormCritereComponent } from './core/pages/form-critere/form-critere.component';
import { GroupsComponent } from './core/pages/groups/groups.component';
import { DonneesComponent } from './core/pages/donnees/donnees.component';
import { ProfilesComponent } from './core/pages/profiles/profiles.component';
import { FormUserComponent } from './core/pages/form-user/form-user.component';
import { FormUserUpdateComponent } from './core/pages/form-user-update/form-user-update.component';
import { NotFoundComponent } from './core/pages/404/404.component';
import { JoursComponent } from './core/pages/jours/jours.component';
import { AttendanceComponent } from './core/pages/attendance/attendance.component';
import { NotesComponent } from './core/pages/notes/notes.component';
import { ArchivedComponent } from './core/pages/archived/archived.component';
import { FormCoachComponent } from './core/pages/form-coach/form-coach.component';
import { JuryComponent } from './core/pages/jury/jury.component';
import { ListeMenuComponent } from './core/components/liste-menu/liste-menu.component';
import { HomeMenuComponent } from './core/components/home-menu/home-menu.component';
import { UserMenuComponent } from './core/components/user-menu/user-menu.component';

export const routes: Routes = [
    {
        path:"authentification/login",
        component:LoginComponent
    },
    {
        path:"app/home",
        component:HomeMenuComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/form-critere",
        component:FormCritereComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/liste-menu",
        component:ListeMenuComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/attendance",
        component:AttendanceComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/form-notes",
        component:NotesComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/form-coach",
        component:FormCoachComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/jury",
        component:JuryComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/liste-select",
        component:SelectlistComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/donnees",
        component:DonneesComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/users",
        component:UserMenuComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/form-user",
        component:FormUserComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/form-update",
        component:FormUserUpdateComponent,
        canActivate: [AuthGuard]
    },
    {
        path:"app/not-found",
        component:NotFoundComponent,
        //canActivate: [AuthGuard]
    },
    {
        path:"",
        redirectTo:"/authentification/login",
        pathMatch:'full'
    }
];