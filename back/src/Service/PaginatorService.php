<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;

class PaginatorService
{
    /**
     * Create a paginated result set from a Doctrine query.
     *
     * @param Query $query The Doctrine query object.
     * @param int $page The current page number (1-based index).
     * @param int $limit The number of items per page.
     *
     * @return Paginator The Doctrine Paginator object.
     */
    public static function pageInator(Query $query, int $page = 1, int $limit = 10): Paginator
    {
        $paginator = new Paginator($query);
        
        $paginator->getQuery()
            ->setFirstResult($limit * $page) 
            ->setMaxResults($limit);

        return $paginator;
    }
}