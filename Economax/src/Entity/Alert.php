<?php

namespace App\Entity;

use App\Enum\TemperatureEnum;
use App\Repository\AlertRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlertRepository::class)]
class Alert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $keyWord = null;

    #[ORM\Column(enumType: TemperatureEnum::class)]
    private ?TemperatureEnum $temperature = TemperatureEnum::ZERO;

    #[ORM\Column]
    private ?bool $isNotify = null;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyWord(): ?string
    {
        return $this->keyWord;
    }

    public function setKeyWord(string $keyWord): self
    {
        $this->keyWord = $keyWord;

        return $this;
    }

    public function getTemperature(): ?TemperatureEnum
    {
        return $this->temperature;
    }

    public function setTemperature(TemperatureEnum $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getTemperatureValue(): ?int
    {
        return $this->temperature->getValue();
    }

    public function isIsNotify(): ?bool
    {
        return $this->isNotify;
    }

    public function setIsNotify(bool $isNotify): self
    {
        $this->isNotify = $isNotify;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
