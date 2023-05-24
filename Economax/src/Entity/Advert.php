<?php

namespace App\Entity;

use App\Repository\AdvertRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdvertRepository::class)]
class Advert extends Deal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?float $usualPrice = null;

    #[ORM\Column(nullable: true)]
    private ?float $shipping = null;

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUsualPrice(): ?float
    {
        return $this->usualPrice;
    }

    public function setUsualPrice(?float $usualPrice): self
    {
        $this->usualPrice = $usualPrice;

        return $this;
    }

    public function getShipping(): ?float
    {
        return $this->shipping;
    }

    public function setShipping(?float $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }
}
