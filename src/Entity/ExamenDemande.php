<?php

namespace App\Entity;

use App\Enum\StatutExamenDemande;
use App\Repository\ExamenDemandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExamenDemandeRepository::class)]
class ExamenDemande
{
      #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'examensDemandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    // Type standardisé ou libellé libre (tu peux remplacer par ton enum TypeExamenComplementaire)
    #[ORM\Column(length: 255)]
    private string $libelle = '';

    #[ORM\Column(nullable: true)]
    private ?bool $urgence = false;

    #[ORM\Column(enumType: StatutExamenDemande::class)]
    private StatutExamenDemande $statut = StatutExamenDemande::DEMANDE;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    public function getId(): ?int { return $this->id; }

    public function getConsultation(): ?Consultation { return $this->consultation; }
    public function setConsultation(?Consultation $consultation): self { $this->consultation = $consultation; return $this; }

    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $libelle): self { $this->libelle = $libelle; return $this; }

    public function isUrgence(): ?bool { return $this->urgence; }
    public function setUrgence(?bool $urgence): self { $this->urgence = $urgence; return $this; }

    public function getStatut(): StatutExamenDemande { return $this->statut; }
    public function setStatut(StatutExamenDemande $statut): self { $this->statut = $statut; return $this; }

    public function getNote(): ?string { return $this->note; }
    public function setNote(?string $note): self { $this->note = $note; return $this; }
}
