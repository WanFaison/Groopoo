<?php

namespace App\Service;

use App\Entity\Etudiant;
use App\Entity\Liste;
use App\Repository\GroupeRepository;
use App\Repository\JuryRepository;
use App\Repository\ListeRepository;
use App\Repository\SalleRepository;
use FPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService{
    private $salleRepository;
    private $groupeRepository;
    private $listeRepository;
    private $juryRepository;
    public function __construct(JuryRepository $juryRepository, SalleRepository $salleRepository, ListeRepository $listeRepository, GroupeRepository $groupeRepository)
    {
        $this->salleRepository = $salleRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
        $this->juryRepository = $juryRepository;
    }

    public function extractNumberFromGroupName(string $groupName): ?int
    {
        if (preg_match('/\d+/', $groupName, $matches)) {
            return (int)$matches[0];
        }

        return null;
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

    public function makeSheet(Liste $liste, $motif)
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
                $sheet->setCellValue('D'.$row, 'Sexe');
                $sheet->setCellValue('E'.$row, 'Niveau');
                $sheet->setCellValue('F'.$row, 'Filiere');
                $sheet->setCellValue('G'.$row, 'Matricule');
                $sheet->setCellValue('H'.$row, 'Note');
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
                    $sheet->setCellValue('D'.$row, $etd->getSexe());
                    $sheet->setCellValue('E'.$row, $etd->getClasse()->getNiveau()->getLibelle());
                    $sheet->setCellValue('F'.$row, $etd->getClasse()->getFiliere()->getLibelle());
                    $sheet->setCellValue('G'.$row, $etd->getMatricule());
                    $sheet->setCellValue('H'.$row, $etd->getNoteFinal());
                }
            }
            $row+=2;
        }

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $liste->getLibelle().'.xlsx');
        $writer->save($temp_file);

        return $temp_file;
    }

    public function makeSheetPerClasse(Liste $liste)
    {
        $etudiants = [];
        foreach($liste->getGroupes() as $g){
            $etudiants = array_merge($etudiants, $g->getEtudiant()->toArray());
        }
        $existingClasses = [];
        foreach($etudiants as $e){
            in_array($e->getClasse(), $existingClasses, false) ? null : $existingClasses[] = $e->getClasse();
        }

        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;
        foreach($existingClasses as $classe){
            $filteredEtds = array_filter($etudiants, function (Etudiant $etudiant) use ($classe) {
                return $etudiant->getClasse() === $classe;});

            $sheetIndex>0 ? $spreadsheet->createSheet():null;
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($classe->getLibelle());

            $row = 3;
            $sheet->setCellValue('A1', $classe->getLibelle());
            $sheet->setCellValue('A2', 'Nom');
            $sheet->setCellValue('B2', 'Prenom');
            $sheet->setCellValue('C2', 'Classe');
            $sheet->setCellValue('D2', 'Sexe');
            $sheet->setCellValue('E2', 'Niveau');
            $sheet->setCellValue('F2', 'Filiere');
            $sheet->setCellValue('G2', 'Matricule');
            $sheet->setCellValue('H2', 'Note');

            foreach($filteredEtds as $etd){
                $sheet->setCellValue('A'.$row, $etd->getNom());
                $sheet->setCellValue('B'.$row, $etd->getPrenom());
                $sheet->setCellValue('C'.$row, $etd->getClasse()->getLibelle());
                $sheet->setCellValue('D'.$row, $etd->getSexe());
                $sheet->setCellValue('E'.$row, $etd->getClasse()->getNiveau()->getLibelle());
                $sheet->setCellValue('F'.$row, $etd->getClasse()->getFiliere()->getLibelle());
                $sheet->setCellValue('G'.$row, $etd->getMatricule());
                $sheet->setCellValue('H'.$row, $etd->getNoteFinal());
                $row++;
            }
            $sheetIndex++;
        }
        
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $liste->getLibelle().'.xlsx');
        $writer->save($temp_file);

        return $temp_file;
    }

    public function makeSheetPerSalle(Liste $liste)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $salles = $this->salleRepository->findAllByEcoleUnarchived($liste->getEcole());
                
        $row = 1;
        foreach ($salles as $salle) {
            $groupes = $this->groupeRepository->findAllByListeGroupeSalleUnArchived($liste, $salle);
            if(count($groupes) >0){
                $sheet->setCellValue('A'.$row, $salle->getLibelle());
                $row++;
                
                $coachGrp = $groupes[0]->getCoach() ? 
                            $groupes[0]->getCoach()->getNom().''.$groupes[0]->getCoach()->getPrenom() : '';
                $sheet->setCellValue('A'.$row, 'Coach:');
                $sheet->setCellValue('B'.$row, $coachGrp);
                $row++;
                
                $sheet->mergeCells("A{$row}:E{$row}");
                $groupNames = '';
                foreach ($groupes as $group) {
                    $groupNames .= $this->extractNumberFromGroupName($group->getLibelle()) . ' - ';
                }
                $groupNames = rtrim($groupNames, ' - ');
                $sheet->setCellValue('A'.$row, $groupNames);
                $row+=2;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $liste->getLibelle().'- Repartition des Coachs.xlsx');
        $writer->save($temp_file);

        return $temp_file;
    }

    public function makeSheetPerJury(Liste $liste)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $jurys = $this->juryRepository->findAllNotFinalByListeUnarchived($liste);
                
        $row = 1;
        foreach ($jurys as $jury) {
            $sheet->mergeCells("A{$row}:A".($row+1));
            $sheet->setCellValue('A'.$row, $jury->getLibelle());
            $row2=$row;
            foreach($jury->getCoaches() as $coach){
                $sheet->setCellValue('B'.$row2, $coach->getNom().''.$coach->getPrenom());
                $row2++;
            }
            $row+=2;
            
            $sheet->mergeCells("A{$row}:E{$row}");
            $groupNames = '';
            foreach ($jury->getGroupes() as $group) {
                $groupNames .= $this->extractNumberFromGroupName($group->getLibelle()) . ' - ';
            }
            $groupNames = rtrim($groupNames, ' - ');
            $sheet->setCellValue('A'.$row, $groupNames);
            $row+=2;
        }

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $liste->getLibelle().'- Repartition des Jury.xlsx');
        $writer->save($temp_file);

        return $temp_file;
    }
    
}
