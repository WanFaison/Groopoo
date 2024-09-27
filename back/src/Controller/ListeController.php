<?php

namespace App\Controller;

use App\Controller\Dto\Response\ListeResponseDto;
use App\Controller\Dto\RestResponse as DtoRestResponse;
use App\Entity\Groupe;
use App\Entity\Liste;
use App\Repository\AnneeRepository;
use App\Repository\ListeRepository;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\GroupeRepository;
use App\Repository\NiveauRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RestResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListeController extends AbstractController
{
    private $ecoleRepository;
    private $anneeRepository;
    private $etudiantRepository;
    private $niveauRepository;
    private $filiereRepository;
    private $classeRepository;
    private $groupeRepository;
    private $listeRepository;

    public function __construct(EcoleRepository $ecoleRepository, AnneeRepository $anneeRepository, EtudiantRepository $etudiantRepository, NiveauRepository $niveauRepository, FiliereRepository $filiereRepository, ClasseRepository $classeRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
        $this->anneeRepository = $anneeRepository;
        $this->etudiantRepository = $etudiantRepository;
        $this->niveauRepository = $niveauRepository;
        $this->filiereRepository = $filiereRepository;
        $this->classeRepository = $classeRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
    }

    #[Route('/api/liste', name: 'api_liste', methods: ['GET'])]
    public function listerListes(Request $request, ListeRepository $listeRepository, AnneeRepository $anneeRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $annee = $request->query->getInt('annee', 0);

        if($annee == 0){
            $listes = $listeRepository->findAllPaginated($page, $limit, $keyword);
        }else{
            $yr = $anneeRepository->find($annee);
            $listes = $listeRepository->findAllPaginated($page, $limit, $keyword, $yr);
        }
        
        $dtos = [];
        foreach ($listes as $liste) {
            $dtos[] = (new ListeResponseDto())->toDto($liste);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived(),
                'annee' => $r->getAnnee(),
                'ecole' => $r->getEcole(),
                'date' => $r->getDate()
            ];
        }

        $totalItems = $listes->count();
        $totalPages = ceil($totalItems / $limit);

        return DtoRestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/liste-find', name: 'api_liste_find', methods: ['GET'])]
    public function findListe(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        if($liste != null){ 
            $dto = (new ListeResponseDto())->toDto($liste); 
            $dto = [
                    'id' => $dto->getId(),
                    'libelle' => $dto->getLibelle(),
                    'isArchived' => $dto->isArchived(),
                    'annee' => $dto->getAnnee(),
                    'ecole' => $dto->getEcole(),
                    'date' => $dto->getDate()
                ];
        }

        return DtoRestResponse::linearResponse($dto, 1, JsonResponse::HTTP_OK);
    }


    #[Route('/api/liste-export', name: 'api_liste_export', methods: ['GET'])]
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $excelFile = $this->makeSheet($liste);

        return new BinaryFileResponse($excelFile);
    }

    public function makeSheet($liste)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $groupes = $this->groupeRepository->findAllByListe($liste);
                
        $row = 1;
        foreach ($groupes as $group) {
            $sheet->setCellValue('A'.$row, $group->getLibelle());
            $row++;
            $sheet->setCellValue('A'.$row, 'Nom');
            $sheet->setCellValue('B'.$row, 'Prenom');
            $sheet->setCellValue('C'.$row, 'Classe');
            $sheet->setCellValue('D'.$row, 'Emargement');
            foreach ($group->getEtudiant() as $etd) {
                $row++;
                $sheet->setCellValue('A'.$row, $etd->getNom());
                $sheet->setCellValue('B'.$row, $etd->getPrenom());
                $sheet->setCellValue('C'.$row, $etd->getClasse()->getLibelle());
            }
            $row+=2;
        }

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $liste->getLibelle().'.xlsx');
        $writer->save($temp_file);

        return $temp_file;
    }
}
