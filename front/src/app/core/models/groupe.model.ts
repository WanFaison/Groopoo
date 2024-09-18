import { EtudiantModel } from "./etudiant.model"

export type GroupeModel = {
    id: number,
    libelle: string,
    liste: number,
    listeT: string,
    etudiants: EtudiantModel[],
    note: string
}
