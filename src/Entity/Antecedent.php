<?php

namespace App\Entity;

use App\Enum\TypeAntecedent;
use App\Repository\AntecedentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AntecedentRepository::class)]
class Antecedent
{
     use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'antecedents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Hospitalisation $hospitalisation;

    #[ORM\Column(enumType: TypeAntecedent::class)]
    private TypeAntecedent $type;

    #[ORM\Column(type: 'text')]
    private string $description;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHospitalisation(): Hospitalisation
    {
        return $this->hospitalisation;
    }

    public function setHospitalisation(Hospitalisation $hospitalisation): self
    {
        $this->hospitalisation = $hospitalisation;

        if (!$hospitalisation->getAntecedents()->contains($this)) {
            $hospitalisation->addAntecedent($this);
        }

        return $this;
    }

    public function getType(): TypeAntecedent
    {
        return $this->type;
    }

    public function setType(TypeAntecedent $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}