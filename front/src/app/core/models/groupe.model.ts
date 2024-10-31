import { EtudiantJourModel, EtudiantModel } from "./etudiant.model"

export type GroupeModel = {
    id: number,
    libelle: string,
    liste: number,
    listeT: string,
    etudiants: EtudiantModel[],
    note: number
}

export type GroupeJourModel = {
    id: number,
    libelle: string,
    liste: number,
    listeT: string,
    etudiants: EtudiantJourModel[],
    note: number
}

export type GroupeReqModel = {
    id: number,
    libelle: string
}
