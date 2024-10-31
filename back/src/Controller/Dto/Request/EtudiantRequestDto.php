<?php

namespace App\Controller\Dto\Request;

use App\Entity\Etudiant;
use App\Enums\AbsenceType;

class EtudiantRequestDto
{
    private int $id;
    private string $matricule;
    private string $nom;
    private string $prenom;
    private string $classe;
    private int $groupe;
    private bool $emargement1 = true;
    private bool $emargement2 = true;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }
    public function setMatricule(string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getClasse(): ?string
    {
        return $this->classe;
    }
    public function setClasse(string $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

    public function getGroupe(): ?int
    {
        return $this->groupe;
    }
    public function setGroupe(int $groupe): static
    {
        $this->groupe = $groupe;
        return $this;
    }

    public function getEmargement1(): ?bool
    {
        return $this->emargement1;
    }
    public function setEmargement1(bool $emargement1): static
    {
        $this->emargement1 = $emargement1;
        return $this;
    }

    public function getEmargement2(): ?bool
    {
        return $this->emargement2;
    }
    public function setEmargement2(bool $emargement2): static
    {
        $this->emargement2 = $emargement2;
        return $this;
    }

    public function toDto(Etudiant $etudiant, array $absences): EtudiantRequestDto
    {
        $dto = new EtudiantRequestDto();

        $dto->setId($etudiant->getId());
        $dto->setNom($etudiant->getNom());
        $dto->setPrenom($etudiant->getPrenom());
        $dto->setMatricule($etudiant->getMatricule());
        $dto->setClasse($etudiant->getClasse()->getLibelle());
        $dto->setGroupe($etudiant->getGroupe()->getId());

        if(count($absences)>0){
            foreach($absences as $a){
                if($a->getType() == AbsenceType::Emargement_1){
                    $dto->setEmargement1(false);
                }else{
                    $dto->setEmargement2(false);
                }
            }
        }

        return $dto;
    }
}
