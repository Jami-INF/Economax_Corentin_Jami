<?php

namespace App\Entity;

use App\Enum\TypeReducEnum;
use App\Repository\PromoCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromoCodeRepository::class)]
class PromoCode extends Deal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, enumType: TypeReducEnum::class)]
    private ?TypeReducEnum $typeReduc = TypeReducEnum::PERCENT;

    public function getTypeReduc(): ?TypeReducEnum
    {
        return $this->typeReduc;
    }

    public function setTypeReduc(TypeReducEnum $typeReduc): self
    {
        $this->typeReduc = $typeReduc;

        return $this;
    }

}
