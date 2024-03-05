<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\Length(min:4, max:50, minMessage: 'The name of your event must be at least 4 characters long', maxMessage: 'the name of your event cannot be longer than 50 characters')]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan('today')]
    #[Assert\GreaterThan(propertyPath: 'registrationDeadline')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan(0)]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan('today')]
    private ?\DateTimeInterface $registrationDeadline = null;

    #[ORM\Column(nullable: false)]
    #[Assert\NotNull()]
    #[Assert\GreaterThan(0)]
    private ?int $maxRegistrations = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotNull()]
    #[Assert\Length(min:20, minMessage: 'Description must be at least 10 characters long')]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancelJustification = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?Place $place = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $eventPlanner = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'events')]
    private Collection $attendants;

    public function __construct()
    {
        $this->attendants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(\DateTimeInterface $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getMaxRegistrations(): ?int
    {
        return $this->maxRegistrations;
    }

    public function setMaxRegistrations(?int $maxRegistrations): static
    {
        $this->maxRegistrations = $maxRegistrations;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCancelJustification(): ?string
    {
        return $this->cancelJustification;
    }

    public function setCancelJustification(?string $cancelJustification): static
    {
        $this->cancelJustification = $cancelJustification;

        return $this;
    }

    public function getStatus(): ?EventStatus
    {
        return $this->status;
    }

    public function setStatus(?EventStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getEventPlanner(): ?User
    {
        return $this->eventPlanner;
    }

    public function setEventPlanner(?User $eventPlanner): static
    {
        $this->eventPlanner = $eventPlanner;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAttendants(): Collection
    {
        return $this->attendants;
    }

    public function addAttendant(User $attendant): static
    {
        if (!$this->attendants->contains($attendant)) {
            $this->attendants->add($attendant);
        }

        return $this;
    }

    public function removeAttendant(User $attendant): static
    {
        $this->attendants->removeElement($attendant);

        return $this;
    }
}
