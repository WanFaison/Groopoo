export type EtudiantModel = {
    id: number,
    matricule: string,
    nom: string,
    prenom: string,
    isArchived: boolean,
    niveau: string,
    filiere: string,
    groupe: string
}

export interface EtudiantCreate{
    matricule: string,
    nom: string,
    prenom: string,
    niveau: string,
    filiere: string,
}
