<?php

namespace App\Controller\Dto\Response;

use App\Entity\Etudiant;

class EtudiantResponseDto
{
    private int $id;
    private string $matricule;
    private string $nom;
    private string $prenom;
    private string $sexe;
    private string $classe;
    private string $niveau;
    private string $filiere;
    private int $groupe;
    private float $noteEtd;
    private float $noteFinal;

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

    public function getSexe(): ?string
    {
        return $this->sexe;
    }
    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

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

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }
    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getFiliere(): ?string
    {
        return $this->filiere;
    }
    public function setFiliere(string $filiere): static
    {
        $this->filiere = $filiere;

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

    public function getNoteEtd(): ?float
    {
        return $this->noteEtd;
    }
    public function setNoteEtd(float $noteEtd): static
    {
        $this->noteEtd = $noteEtd;
        return $this;
    }

    public function getNoteFinal(): ?float
    {
        return $this->noteFinal;
    }
    public function setNoteFinal(float $noteFinal): static
    {
        $this->noteFinal = $noteFinal;
        return $this;
    }

    public function toDto(Etudiant $etudiant): EtudiantResponseDto
    {
        $dto = new EtudiantResponseDto();

        $dto->setId($etudiant->getId());
        $dto->setNom($etudiant->getNom());
        $dto->setPrenom($etudiant->getPrenom());
        $dto->setMatricule($etudiant->getMatricule());
        $dto->setSexe($etudiant->getSexe());
        $dto->setClasse($etudiant->getClasse()->getLibelle());
        $dto->setNiveau($etudiant->getClasse()->getNiveau()->getLibelle());
        $dto->setFiliere($etudiant->getClasse()->getFiliere()->getLibelle());
        $dto->setGroupe($etudiant->getGroupe()->getId());

        $cc = $etudiant->getNoteEtd();
        $ex = $etudiant->getNoteFinal();
        $dto->setNoteEtd($cc !== null ? $cc : 0);
        $dto->setNoteFinal($ex !== null ? $ex : 0);

        return $dto;
    }
}
