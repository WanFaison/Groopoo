<?php

namespace App\Controller;

use App\Controller\Dto\Response\EtudiantResponseDto;
use App\Controller\Dto\RestResponse;
use App\Repository\EtudiantRepository;
use App\Repository\GroupeRepository;
use App\Repository\ListeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EtudiantController extends AbstractController
{
    private $etudiantRepository;
    private $groupeRepository;
    private $listeRepository;

    public function __construct(EtudiantRepository $etudiantRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->etudiantRepository = $etudiantRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
    }

    #[Route('/api/liste-etudiant', name: 'api_etudiant_liste', methods: ['GET'])]
    public function listeEtudiants(Request $request): JsonResponse
    {
        $liste = $request->query->getInt('liste', 0);
        $groupes = $this->groupeRepository->findAllByListe($this->listeRepository->find($liste));

        $etudiants = [];
        foreach ($groupes as $grp) {
            foreach($grp->getEtudiant() as $e){
                $edt = (new EtudiantResponseDto())->toDto($e);
                $etudiants[] = [
                    'Matricule' => $edt->getMatricule(),
                    'Nom' => $edt->getNom(),
                    'Prenom' => $edt->getPrenom(),
                    'Sexe' => $edt->getSexe(),
                    'Nationalite' => $edt->getNationalite(),
                    'Classe' => $edt->getClasse(),
                    'Niveau' => $edt->getNiveau(),
                    'Filiere' => $edt->getFiliere(),
                ];
            }
        }

        return RestResponse::linearResponse($etudiants, count($etudiants), JsonResponse::HTTP_OK);
    }
}
