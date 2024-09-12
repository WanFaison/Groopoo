<?php

namespace App\Controller;

use App\Controller\Dto\RestResponse;
use App\Entity\Liste;
use App\Repository\AnneeRepository;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\GroupeRepository;
use App\Repository\NiveauRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GroupeController extends AbstractController
{
    private $ecoleRepository;
    private $anneeRepository;
    private $etudiantRepository;
    private $niveauRepository;
    private $filiereRepository;
    private $classeRepository;
    private $groupeRepository;

    public function __construct(EcoleRepository $ecoleRepository, AnneeRepository $anneeRepository, EtudiantRepository $etudiantRepository, NiveauRepository $niveauRepository, FiliereRepository $filiereRepository, ClasseRepository $classeRepository, GroupeRepository $groupeRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
        $this->etudiantRepository = $etudiantRepository;
        $this->niveauRepository = $niveauRepository;
        $this->filiereRepository = $filiereRepository;
        $this->classeRepository = $classeRepository;
        $this->groupeRepository = $groupeRepository;
    }

    #[Route('/api/liste-groupe', name: 'app_groupe', methods: ['POST'])]
    public function createGroups(Request $request): JsonResponse
    {
        if (!$request->getContent()) {
            return new JsonResponse(['error' => 'No content'], 400);
        }

        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $ecole = $data['ecole'] ?? 0;
        $annee = $data['annee'] ?? 0;
        $taille = $data['taille'] ?? 0;
        $nom = $data['nom'] ?? '';
        $etudiants = $data['etudiants'] ?? [];
        $criteres = $data['criteres'] ?? [];
        $status = $data['status'] ?? 500;

        return RestResponse::requestResponse('Data received and list created', $data, JsonResponse::HTTP_OK);
    }

    private function manageData($ecole, $annee, $taille, $nom, $etudiants, $criteres): void
    {
        $liste = new Liste();
        $liste->setEcole($this->ecoleRepository->find($ecole));

    }
}
