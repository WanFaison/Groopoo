export type EtudiantModel = {
    id: number,
    matricule: string,
    nom: string,
    prenom: string,
    sexe: string,
    classe: string,
    groupe?: number,
    noteEtd?: number,
    noteFinal?: number
}

export type EtudiantJourModel = {
    id: number,
    matricule: string,
    nom: string,
    prenom: string,
    classe: string,
    groupe: number,
    emargement1: boolean,
    emargement2: boolean
}

export interface EtudiantCreate{
    matricule: string,
    nom: string,
    prenom: string,
    sexe: string,
    niveau: string,
    filiere: string,
    classe: string,
}

export interface EtudiantImportXlsx{
    matricule: string,
    nom?: string,
    prenom?: string,
    sexe?: string,
    niveau?: string,
    filiere?: string,
    classe: string
}

export interface EtudiantCreateXlsx{
    Matricule?: string,
    Nom: string,
    Prenom: string,
    Sexe: string,
    Niveau: string,
    Filiere: string,
    Classe?: string,
}
