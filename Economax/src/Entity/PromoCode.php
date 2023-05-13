<?php

namespace App\Entity;

use App\Repository\PromoCodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromoCodeRepository::class)]
class PromoCode extends Deal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeReduc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeReduc(): ?string
    {
        return $this->typeReduc;
    }

    public function setTypeReduc(string $typeReduc): self
    {
        $this->typeReduc = $typeReduc;

        return $this;
    }
}
