<?php

namespace App\Controller;

use App\Controller\Dto\Response\ProfileResponseDto;
use App\Controller\Dto\RestResponse;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/api/lister-profile', name: 'api_profile', methods: ['GET'])]
    public function listerProfilesPg(Request $request, ProfileRepository $profileRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $profiles = $profileRepository->findAllPaginated($page, $limit, $keyword);
        
        $dtos = [];
        foreach ($profiles as $prof) {
            $dtos[] = (new ProfileResponseDto())->toDto($prof);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle()
            ];
        }

        $totalItems = $profiles->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/profile-liste', name: 'app_profile_liste', methods: ['GET'])]
    public function listerProfiles(ProfileRepository $profileRepository): JsonResponse
    {
        $profiles = $profileRepository->findAllUnarchived();
        $dtos = [];
        foreach ($profiles as $pro) {
            $dtos[] = (new ProfileResponseDto())->toDto($pro);
        } 
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle()
            ];
        }

        $totalItems = count($profiles);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }
}
