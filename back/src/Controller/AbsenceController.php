<?php

namespace App\Controller;

use App\Controller\Dto\RestResponse;
use App\Entity\Absence;
use App\Entity\Etudiant;
use App\Entity\Jour;
use App\Enums\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\AnneeRepository;
use App\Repository\EcoleRepository;
use App\Repository\EtudiantRepository;
use App\Repository\GroupeRepository;
use App\Repository\JourRepository;
use App\Repository\ListeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AbsenceController extends AbstractController
{
    private $groupeRepository;
    private $listeRepository;
    private $entityManager;
    private $jourRepository;
    private $absenceRepository;
    private $etudiantRepository;

    public function __construct(EntityManagerInterface $entityManager, AbsenceRepository $absenceRepository, JourRepository $jourRepository, EtudiantRepository $etudiantRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->etudiantRepository = $etudiantRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
        $this->jourRepository = $jourRepository;
        $this->absenceRepository = $absenceRepository;
    }

    #[Route('/api/mark-absence', name: 'app_mark_absence', methods: ['POST'])]
    public function markAbsences(Request $request): JsonResponse
    {
        if (!$request->getContent()) {
            return new JsonResponse(['error' => 'No content'], 400);
        }
        $data = json_decode($request->getContent(), true);
        $jourId = $data['jour'] ?? 0;
        $absences = $data['absences'] ?? [];

        if (!is_array($absences)) {
            return new JsonResponse(['error' => 'Invalid absences data format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $this->manageAbsences($jourId, $absences);

        try {
            return RestResponse::requestResponse('Data received and absences accounted for', $jourId, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function manageAbsences($jourId, array $absences)
    {
        $jour = $this->jourRepository->find($jourId);
        $i = 0;
        foreach($absences as $abs){
            $etudiant = $this->etudiantRepository->find($abs['id']);
            $etdAbs = $this->absenceRepository->findAllByJourAndEtudiant($jour, $etudiant);
            if((!$abs['emargement1']) && (!$this->checkExistOnJour('emargement1', $etdAbs))){
                $newAbsence = new Absence();
                $newAbsence->setJour($jour)
                            ->setEtudiant($etudiant)
                            ->setArchived(false)
                            ->setType(AbsenceType::Emargement_1);
                $this->entityManager->persist($newAbsence);
                $i++;
            }
            if((!$abs['emargement2']) && (!$this->checkExistOnJour('emargement2', $etdAbs))){
                $newAbsence = new Absence();
                $newAbsence->setJour($jour)
                            ->setEtudiant($etudiant)
                            ->setArchived(false)
                            ->setType(AbsenceType::Emargement_2);
                $this->entityManager->persist($newAbsence);
                $i++;
            }
            if($i % 10 === 0){
                $this->entityManager->flush();
            }
        }
        $this->entityManager->flush();
    }
    public function checkExistOnJour(string $typeToCheck, ?array $etdAbsences=null): bool
    {
        foreach($etdAbsences as $abs){
            if($typeToCheck == 'emargement1'){
                if($abs->getType() == AbsenceType::Emargement_1){
                    return true;
                }
            }else{
                if($abs->getType() == AbsenceType::Emargement_2){
                    return true;
                }
            }
        }

        return false;
    }
}
