import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { AccueilComponent } from './Accueil/accueil.component';
import { FooterComponent } from './Footer/footer.component';
import { HeaderComponent } from './Header/header.component';
import { LoginComponent } from './login/login.component';
import { CreerProjetComponent } from './creer-projet/creer-projet.component';
import { PreviewComponent } from './preview/preview.component';
import { ProjetSelectionComponent } from './projet-selection/projet-selection.component';
import { HeaderGrandProjetComponent } from './header-grand-projet/header-grand-projet.component';
import { Header1Component } from './header1/header1.component';
import { GestionJourneeComponent } from './gestion-journee/gestion-journee.component';
import { GestionNotesComponent } from './gestion-notes/gestion-notes.component';
import { GestionCoachsComponent } from './gestion-coachs/gestion-coachs.component';
import { GestionSallesComponent } from './gestion-salles/gestion-salles.component';
import { LesJurysComponent } from './les-jurys/les-jurys.component';
import { JuryFinalisteComponent } from './jury-finaliste/jury-finaliste.component';
import { GestionGroupeComponent } from './gestion-groupe/gestion-groupe.component';
import { GroupeEmargementComponent } from './groupe-emargement/groupe-emargement.component';
import { ProjetListComponent } from './projet-list/projet-list.component';
import { CreerprojetSelectComponent } from './creerprojet-select/creerprojet-select.component';
import { HeaderBaseDeDonneeComponent } from './header-base-de-donnee/header-base-de-donnee.component';
import { DonneeAnneeComponent } from './donnee-annee/donnee-annee.component';
import { DonneeOrganisationComponent } from './donnee-organisation/donnee-organisation.component';
import { DonneeCoachsComponent } from './donnee-coachs/donnee-coachs.component';
import { DonneeSallesComponent } from './donnee-salles/donnee-salles.component';
import { DonneeEtagesComponent } from './donnee-etages/donnee-etages.component';
import { ImportationGroupeComponent } from './importation-groupe/importation-groupe.component';
import { InterfaceUtilisateursComponent } from './interface-utilisateurs/interface-utilisateurs.component';
import { InterfaceUtilisateursBloquerComponent } from './interface-utilisateurs-bloquer/interface-utilisateurs-bloquer.component';
import { HeaderUtilisateursComponent } from './header-utilisateurs/header-utilisateurs.component';

@NgModule({
  declarations: [
    AppComponent,
    AccueilComponent,
    FooterComponent,
    HeaderComponent,
    LoginComponent,
    CreerProjetComponent,
    PreviewComponent,
    ProjetSelectionComponent,
    HeaderGrandProjetComponent,
    Header1Component,
    GestionJourneeComponent,
    GestionNotesComponent,
    GestionCoachsComponent,
    GestionSallesComponent,
    LesJurysComponent,
    JuryFinalisteComponent,
    GestionGroupeComponent,
    GroupeEmargementComponent,
    ProjetListComponent,
    CreerprojetSelectComponent,
    HeaderBaseDeDonneeComponent,
    DonneeAnneeComponent,
    DonneeOrganisationComponent,
    DonneeCoachsComponent,
    DonneeSallesComponent,
    DonneeEtagesComponent,
    ImportationGroupeComponent,
    InterfaceUtilisateursComponent,
    InterfaceUtilisateursBloquerComponent,
    HeaderUtilisateursComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
