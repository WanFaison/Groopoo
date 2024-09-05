<?php

namespace App\Controller\Dto;

use Symfony\Component\HttpFoundation\JsonResponse;

class RestResponse
{
    public static function paginateResponse($results, $currentPage, $totalItems, $totalPages, $status): JsonResponse
    {
        return new JsonResponse([
                    'results' => $results,
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage,
                    'status' => $status
                ]);
    }

    public static function linearResponse($results, $totalItems, $status): JsonResponse
    {
        return new JsonResponse([
                    'results' => $results,
                    'totalItems' => $totalItems,
                    'status' => $status
        ]);
    }
}
