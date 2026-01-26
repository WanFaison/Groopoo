import { CoachModel } from "./coach.model"
import { GroupeModel, GroupeReqModel } from "./groupe.model"
import { ThemeFinalistModel } from "./theme.model"

export type JuryModel={
    id: number,
    libelle: string,
    coachs?: CoachModel[],
    groupes?: GroupeReqModel[]
}

export type JuryFinalModel={
    id: number,
    libelle: string,
    coachs?: CoachModel[],
    themes?: ThemeFinalistModel[]
}
