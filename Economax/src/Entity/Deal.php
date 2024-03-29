<?php

namespace App\Entity;

use App\Repository\DealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[SoftDeleteable(fieldName: 'expireAt', timeAware: false)]
#[ORM\Entity(repositoryClass: DealRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: Types::STRING)]
#[ORM\DiscriminatorMap([
    'advert' => Advert::class,
    'promoCode' => PromoCode::class,
])]
#[Vich\Uploadable]
class Deal
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['deal:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $expireAt = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $promoCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['deal:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['deal:read'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'deals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'deals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $groups = null;

    #[ORM\OneToMany(mappedBy: 'deal', targetEntity: Comment::class)]
    private Collection $comments;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\OneToMany(mappedBy: 'deal', targetEntity: Temperature::class)]
    private Collection $temperatures;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorites')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'deals')]
    private ?Marchand $marchand = null;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->temperatures = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPromoCode(): ?string
    {
        return $this->promoCode;
    }

    public function setPromoCode(?string $promoCode): self
    {
        $this->promoCode = $promoCode;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * getShortDescription() returns the first 400 characters of the description
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return substr($this->description, 0, 400) . '...';
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getGroups(): ?Group
    {
        return $this->groups;
    }

    public function setGroups(?Group $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setDeal($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getDeal() === $this) {
                $comment->setDeal(null);
            }
        }

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @return Collection<int, Temperature>
     */
    public function getTemperatures(): Collection
    {
        return $this->temperatures;
    }

    public function addTemperature(Temperature $temperature): self
    {
        if (!$this->temperatures->contains($temperature)) {
            $this->temperatures->add($temperature);
            $temperature->setDeal($this);
        }

        return $this;
    }

    public function removeTemperature(Temperature $temperature): self
    {
        if ($this->temperatures->removeElement($temperature)) {
            // set the owning side to null (unless already changed)
            if ($temperature->getDeal() === $this) {
                $temperature->setDeal(null);
            }
        }

        return $this;
    }

    /**
     * getSumTemperatures() returns the sum of all temperatures
     * @return int
     */
    public function getSumTemperatures(): int
    {
        $sum = 0;
        foreach ($this->temperatures as $temperature) {
            $sum += $temperature->getValue();
        }
        return $sum;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addFavorite($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFavorite($this);
        }

        return $this;
    }

    public function getMarchand(): ?Marchand
    {
        return $this->marchand;
    }

    public function setMarchand(?Marchand $marchand): self
    {
        $this->marchand = $marchand;

        return $this;
    }

}
