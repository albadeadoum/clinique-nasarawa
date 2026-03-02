<?php

namespace App\Entity;

use App\Repository\Cim10CodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Cim10CodeRepository::class)]
class Cim10Code
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private string $code = '';

    #[ORM\Column(length: 255)]
    private string $libelle = '';

    public function getId(): ?int { return $this->id; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): self { $this->code = strtoupper(trim($code)); return $this; }

    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $libelle): self { $this->libelle = $libelle; return $this; }

    public function __toString(): string
    {
        return $this->code.' - '.$this->libelle;
    }
}
