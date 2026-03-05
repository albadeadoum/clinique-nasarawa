<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $facture = null;

    #[ORM\Column(type: 'integer')]
    private int $montant = 0;

    #[ORM\Column(length: 20)]
    private string $mode; // ESPECE / MOBILE_MONEY / CARTE
    
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $payeLe;

    public function getId(): ?int
    {
        return $this->id;
    }
}
