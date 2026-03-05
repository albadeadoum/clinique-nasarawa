<?php

namespace App\Entity;

use App\Enum\ModePaiement;
use App\Enum\StatutPaiement;
use App\Repository\FactureRepository;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Facture
{
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'facture')]
    #[ORM\JoinColumn(nullable: false)]
    private Consultation $consultation;

    #[ORM\Column]
    private float $montant;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $dateEmission;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $datePaiement = null;

    #[ORM\Column(enumType: StatutPaiement::class)]
    private StatutPaiement $statutPaiement = StatutPaiement::EN_ATTENTE;

    #[ORM\Column(type: 'string', enumType: ModePaiement::class, nullable: true)]
    private ?ModePaiement $modePaiement = null;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: FactureLigne::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->dateEmission = new \DateTimeImmutable();
    }

    /** @return Collection<int, FactureLigne> */
    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(FactureLigne $ligne): self
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setFacture($this);
        }
        return $this;
    }

    public function clearLignes(): void
    {
        $this->lignes->clear(); // orphanRemoval => suppression DB après flush
    }

    public function recalcMontant(): void
    {
        $sum = 0.0;
        foreach ($this->lignes as $l) {
            $sum += (float) $l->getTotal();
        }
        $this->montant = $sum; // float chez toi (ok pour l’instant)
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsultation(): Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(Consultation $consultation): self
    {
        $this->consultation = $consultation;

        if ($consultation->getFacture() !== $this) {
            $consultation->setFacture($this);
        }

        return $this;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateEmission(): \DateTimeImmutable
    {
        return $this->dateEmission;
    }

    public function setDateEmission(\DateTimeImmutable $dateEmission): self
    {
        $this->dateEmission = $dateEmission;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeImmutable
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(?\DateTimeImmutable $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getStatutPaiement(): StatutPaiement
    {
        return $this->statutPaiement;
    }

    public function setStatutPaiement(StatutPaiement $statutPaiement): self
    {
        $this->statutPaiement = $statutPaiement;

        return $this;
    }

    public function getModePaiement(): ?ModePaiement
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?ModePaiement $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }
}
