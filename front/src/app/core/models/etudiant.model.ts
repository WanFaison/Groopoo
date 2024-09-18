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
    nationalite: string
}
