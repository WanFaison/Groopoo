import { CoachModel } from "./coach.model"
import { GroupeModel, GroupeReqModel } from "./groupe.model"

export type JuryModel={
    id: number,
    libelle: string,
    coachs?: CoachModel[],
    groupes?: GroupeReqModel[]
}