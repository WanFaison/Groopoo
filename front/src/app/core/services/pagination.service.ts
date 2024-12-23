import { Injectable } from "@angular/core";

@Injectable({
  providedIn: 'root'
})

export class PaginatorService{
    constructor(){}

    pages(start: number, end: number | undefined = 5): number[] {
        return Array(end - start + 1).fill(0).map((_, idx) => start + idx);
    }
    getPageRange(currentPage:any, totalPages:any): number[] {
        const start = Math.max(currentPage - 3, 1);
        const end = Math.min(currentPage + 3, totalPages);
        
        return this.pages(start, end);
    }

    reloadPage() {
        window.location.reload();
    }
}
