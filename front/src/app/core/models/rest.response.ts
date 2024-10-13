export interface RestResponse<T>{
    totalItems?: number,
    totalPages?: number,
    currentPage?: number,
    results: T
    status: number
}

export type RequestResponse = {
    message: string,
    data: any,
    status: number
}

export interface AuthResponse{
    message: string,
    status: number
}

export interface Country {
    nom: string;
    code: string;
  }
