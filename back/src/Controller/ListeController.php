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
use App\Repository\UserRepository;
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
    private $ecoleRepository;
    private $anneeRepository;
    private $etudiantRepository;
    private $niveauRepository;
    private $filiereRepository;
    private $classeRepository;
    private $groupeRepository;
    private $listeRepository;
    private $userRepository;

    public function __construct(UserRepository $userRepository, EcoleRepository $ecoleRepository, AnneeRepository $anneeRepository, EtudiantRepository $etudiantRepository, NiveauRepository $niveauRepository, FiliereRepository $filiereRepository, ClasseRepository $classeRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
        $this->anneeRepository = $anneeRepository;
        $this->etudiantRepository = $etudiantRepository;
        $this->niveauRepository = $niveauRepository;
        $this->filiereRepository = $filiereRepository;
        $this->classeRepository = $classeRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
        $this->userRepository = $userRepository;
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
                'isComplet' => $r->isComplete()
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
                    'isComplet' => $dto->isComplete()
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
        $excelFile = $this->makeSheet($liste, $motif);

        return new BinaryFileResponse($excelFile);
    }

    #[Route('/api/liste-export-pdf', name: 'api_liste_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request): Response
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $pdfContent = $this->makePdf($listeId);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $liste->getLibelle() . '.pdf"');
        $response->headers->set('X-Liste-Libelle', $liste->getLibelle());

        return $response;
    }

    public function makeSheet($liste, $motif)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $groupes = $this->groupeRepository->findAllByListe($liste);
                
        $row = 1;
        foreach ($groupes as $group) {
            $sheet->setCellValue('A'.$row, $group->getLibelle());
            if($motif == 'results'){
                $sheet->setCellValue('B'.$row, $group->getNote());
            }
            $row++;
            $sheet->setCellValue('A'.$row, 'Nom');
            $sheet->setCellValue('B'.$row, 'Prenom');
            $sheet->setCellValue('C'.$row, 'Classe');

            if($motif == 'results'){
                $sheet->setCellValue('D'.$row, 'Note');
            }else{
                $sheet->setCellValue('D'.$row, 'Emargement 1');
                $sheet->setCellValue('E'.$row, 'Emargement 2');
            }
            
            foreach ($group->getEtudiant() as $etd) {
                $row++;
                $sheet->setCellValue('A'.$row, $etd->getNom());
                $sheet->setCellValue('B'.$row, $etd->getPrenom());
                $sheet->setCellValue('C'.$row, $etd->getClasse()->getLibelle());
                if($motif == 'results'){
                    $sheet->setCellValue('D'.$row, $etd->getNoteFinal());
                }
            }
            $row+=2;
        }

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $liste->getLibelle().'.xlsx');
        $writer->save($temp_file);

        return $temp_file;
    }

    public function makePdf($liste): string
    {
        $listeT = $this->listeRepository->find($liste);
        $groupes = $listeT->getGroupes();
        
        $pdf = new FPDF('L');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, $listeT->getLibelle(), 0, 1, 'C');

        foreach ($groupes as $g) {
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, $g->getLibelle(), 0, 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 10, 'Nom', 1);
            $pdf->Cell(50, 10, 'Prenom', 1); 
            $pdf->Cell(50, 10, 'Classe', 1);
            $pdf->Cell(30, 10, 'Emargement 1', 1);
            $pdf->Cell(30, 10, 'Emargement 2', 1);
            $pdf->Ln(); 

            foreach ($g->getEtudiant() as $etudiant) {
                $pdf->Cell(40, 10, $etudiant->getNom(), 1); 
                $pdf->Cell(50, 10, $etudiant->getPrenom(), 1);
                $pdf->Cell(50, 10, $etudiant->getClasse()->getLibelle(), 1);  
                $pdf->Cell(30, 10, '', 1);  
                $pdf->Cell(30, 10, '', 1);    
                $pdf->Ln();
            }
        }

        return $pdf->Output('S');
    }
}
