<?php

namespace App\Entity;

use App\Repository\ActeRealiseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActeRealiseRepository::class)]
class ActeRealise
{
     #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'actesRealises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\Column(length: 255)]
    private string $libelle = '';

    #[ORM\Column]
    private int $quantite = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    public function getId(): ?int { return $this->id; }

    public function getConsultation(): ?Consultation { return $this->consultation; }
    public function setConsultation(?Consultation $consultation): self { $this->consultation = $consultation; return $this; }

    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $libelle): self { $this->libelle = $libelle; return $this; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): self { $this->quantite = max(1, $quantite); return $this; }

    public function getPrixUnitaire(): ?string { return $this->prixUnitaire; }
    public function setPrixUnitaire(?string $prixUnitaire): self { $this->prixUnitaire = $prixUnitaire; return $this; }

    public function getNote(): ?string { return $this->note; }
    public function setNote(?string $note): self { $this->note = $note; return $this; }
}
