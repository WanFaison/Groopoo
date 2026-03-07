<?php

namespace App\Service;

use App\Entity\Liste;
use App\Repository\GroupeRepository;
use App\Repository\ListeRepository;
use Doctrine\ORM\EntityManagerInterface;

class NoteService{
    private $entityManager;
    private $groupeRepository;
    private $listeRepository;
    public function __construct(EntityManagerInterface $entityManager, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
    }

    public function manageNotes(array $notes): Liste
    {
        $grp = $this->groupeRepository->find($notes[0]['id']);
        $pts = $this->marksPerPeriod($grp->getListe());
        foreach($notes as $note){
            $groupe = $this->groupeRepository->find($note['id']);
            $groupe->setNote($this->checkNote((float) $note['note']));
            foreach($groupe->getEtudiant() as $etd){
                $noteEtd = (float) $note['note'];
                foreach($etd->getAbsences() as $abs){
                    $noteEtd = $noteEtd - $pts;
                }
                $etd->setNoteEtd($this->checkNote($noteEtd));
                $final = 0.0;
                if($noteEtd >= 1){$final = round((((float) $note['note']) + ($this->checkNote($noteEtd))) / 2.0, 2);}
                
                $etd->setNoteFinal($this->checkNote($final));
                $this->entityManager->persist($etd);
            }
            $this->entityManager->flush();
            $this->groupeRepository->addOrUpdate($groupe);
        }

        return $grp->getListe();
    }
    private function marksPerPeriod(Liste $liste): float
    {
        $periods = ($liste->getJours()->count())*2;
        if($periods>0){
            return  20.0/$periods;
        }
        return 20.0;
    }
    private function checkNote(float $note):float
    {
        if($note < 0){
            return 0.0;
        }
        return $note;
    }

    public function setToFinal(array $groupes, bool $final): array
    {
        foreach($groupes as $grp){
            $grp->setFinal($final);
            $this->entityManager->persist($grp);
        }
        $this->entityManager->flush();
        return $groupes;
    }

    public function hasGroupWithNoteAboveZero(array $groupes): bool
    {
        foreach ($groupes as $groupe) {
            if ($groupe->getNote() > 0) {return true;}
        }
        return false;
    }

    public function saveNotesInDB(Liste $liste)
    {
        $notesDB = [];
        $groups = $liste->getGroupes()->toArray();

        foreach($groups as $grp){
            array_push($notesDB, ['id'=>$grp->getId(), 'note'=>$grp->getNote()]);
        }
        $liste->setNotes($notesDB);
        $this->listeRepository->addOrUpdate($liste);
    }
}
