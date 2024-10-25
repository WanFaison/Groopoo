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
        $userId = $request->query->getInt('user', 0);
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $annee = $request->query->getInt('annee', 0);
        $ecole = $request->query->getInt('ecole', 0);

        $user = $this->userRepository->find($userId);
        if($user->getEcole()){$ecole = $user->getEcole()->getId();}

        if($annee == 0){$annee = null;} 
        if($ecole == 0){$ecole = null;} 
        $listes = $listeRepository->findAllPaginated($page, $limit, $keyword, $annee, $ecole);
        
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
                    'critere' => $dto->getCritere(),
                    'annee' => $dto->getAnnee(),
                    'ecole' => $dto->getEcole(),
                    'date' => $dto->getDate()
                ];
        }

        return DtoRestResponse::linearResponse($dto, 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/liste-modif', name: 'api_liste_modif', methods: ['GET'])]
    public function archiveList(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $keyword = $request->query->getString('keyword', '');

        $liste = $this->listeRepository->find($listeId);
        if($keyword != ''){
            if($this->listeRepository->findByLibelle($keyword)){
                return DtoRestResponse::requestResponse('Cette liste existe deja', 1, JsonResponse::HTTP_OK);
            }
            $liste->setLibelle($keyword);
        }else{
            $liste->setArchived(true);
        }

        $this->listeRepository->addOrUpdate($liste);
        
        return DtoRestResponse::requestResponse('List has been archived', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/liste-export', name: 'api_liste_export', methods: ['GET'])]
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $excelFile = $this->makeSheet($liste);

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
            $sheet->setCellValue('D'.$row, 'Emargement 1');
            $sheet->setCellValue('E'.$row, 'Emargement 2');
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
