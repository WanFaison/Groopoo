<?php

namespace App\Controller;

use App\Controller\Dto\Response\ThemeResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Theme;
use App\Entity\Groupe;
use App\Entity\Liste;
use App\Repository\ThemeRepository;
use App\Repository\ListeRepository;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ThemeController extends AbstractController
{
    private $themeRepository;
    private $listeRepository;
    private $groupeRepository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ThemeRepository $themeRepository, ListeRepository $listeRepository, GroupeRepository $groupeRepository)
    {
        $this->entityManager = $entityManager;
        $this->themeRepository = $themeRepository;
        $this->listeRepository = $listeRepository;
        $this->groupeRepository = $groupeRepository;
    }

    // #[Route('/api/find-theme', name: 'app_theme_find', methods: ['GET'])]
    // public function findTheme(Request $request): JsonResponse
    // {

    // }

    #[Route('/api/liste-theme', name: 'app_theme_liste', methods: ['GET'])]
    public function listerThemePg(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');

        $themes = $this->themeRepository->findAllPaginatedUnarchived($page, $limit, $keyword);
        $dtos = [];
        foreach ($themes as $theme) {
            $dtos[] = (new ThemeResponseDto())->toDto($theme);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle()
            ];
        }

        $totalItems = $themes->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/theme-liste', name: 'app_liste_theme', methods: ['GET'])]
    public function listerTheme(Request $request): JsonResponse
    {
        $themes = $this->themeRepository->findAll();
        $dtos = [];
        foreach ($themes as $theme) {
            $dtos[] = (new ThemeResponseDto())->toDto($theme);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle()
            ];
        }

        $totalItems = count($themes);
        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/add-theme', name: 'api_add_theme', methods: ['POST'])]
    public function addTheme(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $libelle = $data['data'] ?? null;

        if($this->themeRepository->checkExistByLibelle($libelle)){
            return RestResponse::requestResponse('Ce theme existe deja', 1, JsonResponse::HTTP_OK);
        }
        if($libelle){
            $theme = new Theme();
            $theme->setLibelle($libelle);
            $theme->setArchived(false);
            $this->themeRepository->addOrUpdate($theme);

            return RestResponse::requestResponse('theme created!', 0, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $libelle, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/theme-modif', name: 'api_theme_modif', methods: ['GET'])]
    public function modifTheme(Request $request): JsonResponse
    {
        $themeId = $request->query->getInt('theme', 0);
        $keyword = $request->query->getString('keyword', '');

        $theme = $this->themeRepository->find($themeId);
        if($keyword == ''){
            $theme->setArchived(true);
        }else{
            if($this->themeRepository->checkExistByLibelle($keyword)){
                return RestResponse::requestResponse('Cette theme existe deja', 1, JsonResponse::HTTP_OK);
            }
            $theme->setLibelle($keyword);
        }
        
        $this->themeRepository->addOrUpdate($theme);
        return RestResponse::requestResponse('Theme has been updated', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/get-theme', name: 'app_theme_get', methods: ['GET'])]
    public function findByListe(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        $themes = [];
        foreach($liste->getGroupes() as $grp){
            $cc=$grp->getTheme();
            in_array($cc, $themes, true)? null:$themes[] = $cc;
        }
        $dtos = [];
        foreach ($themes as $theme) {
            $theme? $dtos[] = (new ThemeResponseDto())->toDto($theme) : null;
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived()
            ];
        }

        $totalItems = count($themes);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/assign-theme', name: 'api_assign_theme', methods: ['POST'])]
    public function assignTheme(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $themes = $data['themes'] ?? [];
        $listeId = $data['liste'] ?? 0;

        $liste = $this->listeRepository->find($listeId);
        $groups = $this->groupeRepository->findAllByListe($liste);

        if(count($themes)<1 || count($themes)>count($groups)){
            return RestResponse::requestResponse('More themes than groups', 1, JsonResponse::HTTP_OK);
        }

        $numGP = intdiv(count($groups) + count($themes) - 1, count($themes));
        $sch = 0;
        $cnt = 0;
        $themes = $this->convertJsonDataToThemeArray($themes);
        foreach ($themes as $theme) {
            $this->checkThemeInListe($theme, $liste);
            while((isset($groups[$cnt])) && ($cnt < $numGP*($sch + 1))){
                $cnt++;
                $grp = $groups[$cnt - 1];
                $theme->addGroupe($grp);
                $this->groupeRepository->addOrUpdate($grp);
            }

            $this->entityManager->persist($theme);
            $this->entityManager->flush();
            $sch++;
        }

        return RestResponse::requestResponse('Data received and used', 0, JsonResponse::HTTP_OK);
    }

    private function convertJsonDataToThemeArray(array $themes):array {
        $themeArray = [];
        foreach($themes as $c){
            $cc = $this->themeRepository->find($c['id']);
            $cc ? $themeArray[]=$cc : null;
        }
        return $themeArray;
    }

    private function checkThemeInListe(Theme $theme, Liste $liste)
    {
        $filteredGrps = array_filter($theme->getGroupes()->toArray(), function (Groupe $groupe) use ($liste) {
            return $groupe->getListe() === $liste;
            });

        foreach($filteredGrps as $grp){
            $theme->removeGroupe($grp);
            $this->entityManager->persist($grp);
        }
        $this->entityManager->persist($theme);
        $this->entityManager->flush();
    }
}
