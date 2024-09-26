export type EtudiantModel = {
    id: number,
    matricule: string,
    nom: string,
    prenom: string,
    sexe: string,
    nationalite: string,
    classe: string,
    groupe: number
}

export interface EtudiantCreate{
    matricule: string,
    nom: string,
    prenom: string,
    sexe: string,
    niveau: string,
    filiere: string,
    classe: string,
    nationalite: string
}

export interface EtudiantCreateXlsx{
    Matricule: string,
    Nom: string,
    Prenom: string,
    Sexe: string,
    Niveau: string,
    Filiere: string,
    Classe: string,
    Nationalite: string
}
