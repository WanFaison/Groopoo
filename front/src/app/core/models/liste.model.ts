export type ListeModel = {
    id: number,
    libelle: string,
    critere: string,
    annee: number,
    ecole: string,
    ecoleId: number,
    date: string,
    count: number,
    isComplet: boolean,
    isArchived: boolean,
    isImport?: boolean
}
