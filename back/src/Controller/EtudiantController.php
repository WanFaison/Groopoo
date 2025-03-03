<?php

namespace App\Controller;

use App\Controller\Dto\Response\EtudiantResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Etudiant;
use App\Repository\ClasseRepository;
use App\Repository\EtudiantRepository;
use App\Repository\GroupeRepository;
use App\Repository\ListeRepository;
use App\Service\ExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EtudiantController extends AbstractController
{
    private $etudiantRepository;
    private $groupeRepository;
    private $listeRepository;
    private $classeRepository;
    private $entityManager;
    private $exportService;

    public function __construct(EntityManagerInterface $entityManager, ExportService $exportService, ClasseRepository $classeRepository, EtudiantRepository $etudiantRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->etudiantRepository = $etudiantRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
        $this->classeRepository = $classeRepository;
        $this->entityManager = $entityManager;
        $this->exportService = $exportService;
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
                    'Classe' => $edt->getClasse(),
                    'Niveau' => $edt->getNiveau(),
                    'Filiere' => $edt->getFiliere(),
                ];
            }
        }

        return RestResponse::linearResponse($etudiants, count($etudiants), JsonResponse::HTTP_OK);
    }

    #[Route('/api/etudiant-find', name: 'api_etudiant_find', methods: ['GET'])]
    public function findEtudiant(Request $request): JsonResponse
    {
        $etudiantId = $request->query->getInt('etudiant', 0);
        $etudiant = $this->etudiantRepository->find($etudiantId);
        if($etudiant != null){ 
            $dto = (new EtudiantResponseDto())->toDto($etudiant); 
            $dto = [
                    'id' => $dto->getId(),
                    'matricule' => $dto->getMatricule(),
                    'nom' => $dto->getNom(),
                    'prenom' => $dto->getPrenom(),
                    'sexe' => $dto->getSexe(),
                    'classe' => $dto->getClasse()
                    ];
        }

        return RestResponse::linearResponse($dto, 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/etudiant-transfer', name: 'api_etudiant_transfer', methods: ['GET'])]
    public function transferEtudiant(Request $request): JsonResponse
    {
        $etudiantId = $request->query->getInt('etudiant', 0);
        $groupeId = $request->query->getInt('groupe', 0);
        $etudiant = $this->etudiantRepository->find($etudiantId);
        $groupe = $this->groupeRepository->find($groupeId);

        if($etudiant && $groupe){
            $groupe->addEtudiant($etudiant);
            $this->groupeRepository->addOrUpdate($groupe);
            $this->etudiantRepository->addOrUpdate($etudiant);
            return RestResponse::requestResponse('etudiant transferer avec succes', 0, JsonResponse::HTTP_OK);
        }else{
            return RestResponse::requestResponse('etudiant ou groupe non-trouve', 1, JsonResponse::HTTP_OK);
        }
    }

    #[Route('/api/etudiant-delete', name: 'api_etudiant_delete', methods: ['GET'])]
    public function deleteEtudiant(Request $request): JsonResponse
    {
        $etudiantId = $request->query->getInt('etudiant', 0);
        $etudiant = $this->etudiantRepository->find($etudiantId);
        foreach($etudiant->getAbsences() as $abs){
            $this->entityManager->remove($abs);
        }
        $this->entityManager->remove($etudiant);
        $this->entityManager->flush();

        return RestResponse::requestResponse('etudiant retirer avec success', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/etudiant-to-liste', name: 'api_etudiant_to_liste', methods: ['POST'])]
    public function addEtudiantToListe(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $groupeId = $data['groupe'] ?? 0;
        $nom = $data['etdForm']['nom'] ?? null;
        $prenom = $data['etdForm']['prenom'] ?? null;
        $matricule = $data['etdForm']['matricule'] ?? null;
        $classeId = $data['etdForm']['classe'] ?? 0;
        $sexe = $data['etdForm']['sexe'] ?? null;

        $groupe = $groupeId!=0 ? $this->groupeRepository->find($groupeId) : null;
        $classe = $classeId!=0 ? $this->classeRepository->find($classeId) : null;
        $newEtd = new Etudiant();
        $newEtd->setArchived(false)
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setMatricule($matricule)
                ->setClasse($classe)
                ->setSexe($sexe ? 'Masculine':'Feminin');
        $this->etudiantRepository->addOrUpdate($newEtd);

        $groupe->addEtudiant($newEtd);
        $this->entityManager->persist($groupe);
        $this->entityManager->persist($newEtd);
        $this->entityManager->flush();

        return RestResponse::requestResponse('etudiant ajoutee avec success', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/etudiant-export', name: 'api_etudiant_export', methods: ['POST'])]
    public function exportEtudiantListe(Request $request): BinaryFileResponse
    {
        $data = json_decode($request->getContent(), true);
        $etudiants = $data['etudiants'] ?? [];

        $excelFile = $this->exportService->makeSheetEtudiantListe($etudiants);

        return new BinaryFileResponse($excelFile);
    }
}
