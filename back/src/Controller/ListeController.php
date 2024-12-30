<?php

namespace App\Controller;

use App\Controller\Dto\Response\ListeResponseDto;
use App\Controller\Dto\RestResponse as DtoRestResponse;
use App\Entity\Etudiant;
use App\Entity\Groupe;
use App\Entity\Liste;
use App\Repository\AnneeRepository;
use App\Repository\ListeRepository;
use App\Repository\ClasseRepository;
use App\Repository\CoachRepository;
use App\Repository\EcoleRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\GroupeRepository;
use App\Repository\NiveauRepository;
use App\Repository\SalleRepository;
use App\Repository\UserRepository;
use App\Service\ExportService;
use Doctrine\ORM\EntityManagerInterface;
use FPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RestResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class ListeController extends AbstractController
{
    private $listeRepository;
    private $ecoleRepository;
    private $entityManager;
    private $exportService;

    public function __construct(EntityManagerInterface $entityManager, EcoleRepository $ecoleRepository, ExportService $exportService, SalleRepository $salleRepository, CoachRepository $coachRepository, UserRepository $userRepository, AnneeRepository $anneeRepository, EtudiantRepository $etudiantRepository, NiveauRepository $niveauRepository, FiliereRepository $filiereRepository, ClasseRepository $classeRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->listeRepository = $listeRepository;
        $this->entityManager = $entityManager;
        $this->exportService = $exportService;
        $this->ecoleRepository = $ecoleRepository;
    }

    #[Route('/api/liste-transfer', name: 'api_liste_transfer', methods: ['GET'])]
    public function transferListe(Request $request): JsonResponse
    {
        $ecoleId = $request->query->getInt('ecole', 0);
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $ecole = $this->listeRepository->find($ecoleId);

        if ($liste && $ecole){
            $ecole->addListe($liste);
            $this->ecoleRepository->addOrUpdate($ecole);
            $this->listeRepository->addOrUpdate($liste);

            return DtoRestResponse::requestResponse('liste transferer', 0, JsonResponse::HTTP_OK);
        } 
        
        return DtoRestResponse::requestResponse('liste non-transferer', 0, JsonResponse::HTTP_OK);
    }
    
    #[Route('/api/liste-delete', name: 'api_liste_delete', methods: ['GET'])]
    public function deleteListe(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        foreach($liste->getGroupes() as $grp){
            foreach($grp->getEtudiant() as $etd){
                foreach($etd->getAbsences() as $abs){
                    $this->entityManager->remove($abs);
                }
                $this->entityManager->remove($etd);
            }
            $this->entityManager->remove($grp);
            $this->entityManager->flush();
        }
        foreach($liste->getJours() as $jr){
            $this->entityManager->remove($jr);
        }
        foreach($liste->getJuries() as $jury){
            $this->entityManager->remove($jury);
        }
        $this->entityManager->flush();

        if($this->listeRepository->deleteById($listeId)){
            return DtoRestResponse::requestResponse('liste supprimer avec success', 0, JsonResponse::HTTP_OK);
        }

        return DtoRestResponse::requestResponse('liste non-trouve', 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/liste', name: 'api_liste', methods: ['GET'])]
    public function listerListes(Request $request, ListeRepository $listeRepository, AnneeRepository $anneeRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $annee = $request->query->getInt('annee', 0);
        $ecole = $request->query->getInt('ecole', 0);
        $archived = $request->query->getInt('archived', 0);

        if($annee == 0){$annee = null;} 
        if($ecole == 0){$ecole = null;} 
        $listes = $listeRepository->findAllPaginated($page, $limit, $keyword, $annee, $ecole, $archived);
        
        $dtos = [];
        foreach ($listes as $liste) {
            $dtos[] = (new ListeResponseDto())->toDto($liste);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'critere' => $r->getCritere(),
                'annee' => $r->getAnnee(),
                'ecole' => $r->getEcole(),
                'date' => $r->getDate(),
                'isComplet' => $r->isComplete(),
                'isImport' => $r->isImported()
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
                    'critere' => $dto->getCritere(),
                    'annee' => $dto->getAnnee(),
                    'ecole' => $dto->getEcole(),
                    'date' => $dto->getDate(),
                    'count' => $dto->getCount(),
                    'isComplet' => $dto->isComplete(),
                    'isArchived' => $dto->isArchived(),
                    'isImport' => $dto->isImported()
                ];
        }

        return DtoRestResponse::linearResponse($dto, 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/liste-modif', name: 'api_liste_modif', methods: ['GET'])]
    public function archiveList(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $keyword = $request->query->getString('keyword', '');
        $motif = $request->query->getString('motif', '');

        $liste = $this->listeRepository->find($listeId);
        if($keyword != ''){
            if($this->listeRepository->findByLibelle($keyword)){
                return DtoRestResponse::requestResponse('Cette liste existe deja', 1, JsonResponse::HTTP_OK);
            }
            $liste->setLibelle($keyword);
        }else if($motif == 'verr'){
            $liste->setComplete(true);
        }else if($motif == 'deverr'){
            $liste->setComplete(false);
        }else{
            $liste->setArchived(!$liste->isArchived());
        }

        $this->listeRepository->addOrUpdate($liste);
        
        return DtoRestResponse::requestResponse('List has been modified', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/template', name: 'api_template', methods: ['GET'])]
    public function makeTemplateSheet():BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
                
        $row = 1;
        $sheet->setCellValue('A'.$row, 'Matricule')
                ->setCellValue('B'.$row, 'Nom')
                ->setCellValue('C'.$row, 'Prenom')
                ->setCellValue('D'.$row, 'Sexe')
                ->setCellValue('E'.$row, 'Classe')
                ->setCellValue('F'.$row, 'Niveau')
                ->setCellValue('G'.$row, 'Filiere')

                ->setCellValue('A2', 'ISM2223/DK-30820')
                ->setCellValue('B2', 'LY')
                ->setCellValue('C2', 'Abdoulaye')
                ->setCellValue('D2', 'Masculin')
                ->setCellValue('E2', 'L2-CDSD')
                ->setCellValue('F2', 'LICENCE 2')
                ->setCellValue('G2', 'CDSD')

                ->setCellValue('A3', 'ISM2223/DK-30805')
                ->setCellValue('B3', 'DOE')
                ->setCellValue('C3', 'John')
                ->setCellValue('D3', 'Féminin')
                ->setCellValue('E3', 'L1-MAIE')
                ->setCellValue('F3', 'LICENCE 1')
                ->setCellValue('G3', 'MAIE');

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'Band-It template.xlsx');
        $writer->save($temp_file);

        return new BinaryFileResponse($temp_file);
    }

    #[Route('/api/template-import', name: 'api_template_import', methods: ['GET'])]
    public function makeImportTemplateSheet():BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
                
        $sheet->setCellValue('A1', 'Group n')
                ->setCellValue('B1', '*entrez la note du groupe ici')
                ->getStyle('B1')->getFont()->setItalic(true);
        $sheet->setCellValue('A2', 'Matricule')
                ->setCellValue('B2', 'Nom')
                ->setCellValue('C2', 'Prenom')
                ->setCellValue('D2', 'Sexe')
                ->setCellValue('E2', 'Classe')
                ->setCellValue('F2', 'Niveau')
                ->setCellValue('G2', 'Filiere');

        $sheet->setCellValue('A4', 'Group 1')
                ->setCellValue('B4', '17');
        $sheet->setCellValue('A5', 'ISM2223/DK-00001')
                ->setCellValue('B5', 'John')
                ->setCellValue('C5', 'Doe')
                ->setCellValue('D5', 'Masculin')
                ->setCellValue('E5', 'L3-GLRS ')
                ->setCellValue('F5', 'LICENCE 3')
                ->setCellValue('G5', 'GLRS')
                
                ->setCellValue('A6', 'ISM2223/DK-00002')
                ->setCellValue('B6', 'Mary')
                ->setCellValue('C6', 'Grace')
                ->setCellValue('D6', 'Féminin')
                ->setCellValue('E6', 'L2-TTL ')
                ->setCellValue('F6', 'LICENCE 2')
                ->setCellValue('G6', 'TTL');

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'Band-It import template.xlsx');
        $writer->save($temp_file);

        return new BinaryFileResponse($temp_file);
    }

    #[Route('/api/liste-export', name: 'api_liste_export', methods: ['GET'])]
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $motif = $request->query->getString('motif', '');
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $motif == 'classes' ? $excelFile = $this->exportService->makeSheetPerClasse($liste) : $excelFile = $this->exportService->makeSheet($liste, $motif);

        return new BinaryFileResponse($excelFile);
    }

    #[Route('/api/coach-export', name: 'api_coach_export', methods: ['GET'])]
    public function exportExcelPerSalle(Request $request): BinaryFileResponse
    {
        $motif = $request->query->getString('motif', '');
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $motif == 'salle' ? $excelFile = $this->exportService->makeSheetPerSalle($liste) : $excelFile = $this->exportService->makeSheetPerJury($liste);

        return new BinaryFileResponse($excelFile);
    }

    #[Route('/api/liste-export-pdf', name: 'api_liste_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request): Response
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $pdfContent = $this->exportService->makePdf($listeId);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $liste->getLibelle() . '.pdf"');
        $response->headers->set('X-Liste-Libelle', $liste->getLibelle());

        return $response;
    }

}
