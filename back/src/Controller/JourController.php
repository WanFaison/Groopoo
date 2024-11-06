<?php

namespace App\Controller;

use App\Controller\Dto\Response\JourResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Jour;
use App\Repository\AbsenceRepository;
use App\Repository\JourRepository;
use App\Repository\ListeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JourController extends AbstractController
{
    private $jourRepository;
    private $listeRepository;
    private $absenceRepository;

    public function __construct(JourRepository $jourRepository, ListeRepository $listeRepository, AbsenceRepository $absenceRepository)
    {
        $this->jourRepository = $jourRepository;
        $this->listeRepository = $listeRepository;
        $this->absenceRepository = $absenceRepository;
    }

    #[Route('/api/find-jour', name: 'app_find_jour', methods: ['GET'])]
    public function findJour(Request $request): JsonResponse
    {
        $jourId = $request->query->getInt('jour', 0);
        $jour = $this->jourRepository->find($jourId);
        $dto = (new JourResponseDto())->toDto($jour);
        $result = [
            'id' => $dto->getId(),
            'libelle' => $dto->getLibelle(),
            'date' => $dto->getDate(),
        ];

        return RestResponse::findRequestResponse('Journee found', $result, JsonResponse::HTTP_OK);
    }

    #[Route('/api/lister-jour', name: 'app_lister_jour', methods: ['GET'])]
    public function listerJours(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        $dtos=[];
        foreach($this->jourRepository->findAllByListeAndUnarchived($liste) as $j){
            $dtos[] = (new JourResponseDto())->toDto($j);
        }
        $results = [];
        foreach($dtos as $r){
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'date' => $r->getDate(),
            ];
        }
        $totalItems = $liste->getJours()->count();

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/add-jour/{liste}', name: 'app_add_jour', methods: ['POST'])]
    public function addJours(Request $request, int $liste): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $y = $data['year'] ?? null;
        $m = $data['month'] ?? null;
        $d = $data['day'] ?? null;
        $dateStr = $y.'-'.$m.'-'.$d;
        $liste = $this->listeRepository->find($liste);
        $date = \DateTime::createFromFormat('Y-m-d', $dateStr);

        if($date){
            foreach($liste->getJours() as $j){
                if($j->getDate()->format('Y-m-d') == $dateStr){
                    return RestResponse::requestResponse('Un jour existe déjà avec cette date et correspondant à la liste actuelle',
                                                            1, JsonResponse::HTTP_OK);
                }
            }
            $jour = new Jour();
            $jour->setDate($date);
            $jour->setArchived(false);
            $jour->setListe($liste);
            $jour->setLibelle('Journée '.(1+$liste->getJours()->count()));

            $this->jourRepository->addOrUpdate($jour);
            return RestResponse::requestResponse('Jour ajouté avec succès', 0, JsonResponse::HTTP_OK);
        }
        return RestResponse::requestResponse('Date invalide', 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/jour-modif', name: 'api_jour_modif', methods: ['GET'])]
    public function archiveJr(Request $request): JsonResponse
    {
        $jourId = $request->query->getInt('jour', 0);

        $jour = $this->jourRepository->find($jourId);
        $liste = $jour->getListe();
        $abs = $jour->getAbsences();
        if($abs->count() >0){
            foreach($abs as $a){
                $a->setArchived(false);
                $this->absenceRepository->addOrUpdate($a);
            }
        }
        $jour->setArchived(true);
        $this->jourRepository->addOrUpdate($jour);
        $this->reOrderJours($this->jourRepository->findAllByListeAndUnarchived($liste));
        
        return RestResponse::requestResponse('Jour has been updated', 0, JsonResponse::HTTP_OK);
    }

    public function sortByDate(array $jrs): array
    {
        usort($jrs, function ($a, $b) {
            $dateA = $a->getDate(); 
            $dateB = $b->getDate();

            if ($dateA === null) return 1; // Push null dates to the end
            if ($dateB === null) return -1;
            
            return $dateA <=> $dateB;
        });

        return $jrs;
    }

    public function reOrderJours(array $jrs)
    {
        $i = 1;
        foreach($this->sortByDate($jrs) as $j){
            $j->setLibelle('Journée '.$i);
            $this->jourRepository->addOrUpdate($j);
            $i++;
        }
    }
}
