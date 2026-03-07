export type UserModel = {
    id: number,
    username: string,
    noms:string,
    email: string,
    ecole:number,
    ecoleT: string,
    roles: []
}

export type LogUser ={
    id: number,
    username: string,
    role: any,
    ecole: any,
    ecoleT: any
}
