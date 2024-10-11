import { ProfileModel } from "./profile.model"

export type UserModel = {
    id: number,
    email: string,
    ecole:number,
    ecoleT: string,
    profiles: ProfileModel[]
}