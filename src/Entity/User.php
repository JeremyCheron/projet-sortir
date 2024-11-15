<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['nickname'], message: 'There is already an account with this nickname')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message:"Not a valid email")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotNull(message:"Mandatory password", groups: ["registration"])]
    #[Assert\Regex(pattern:'/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/',
        message:"The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character ( @, $, !, %, *, \#, ?, &). :"
    ,groups: ["registration"])]
    #[Assert\Length(
        min:6,
        minMessage:"Minimum of 6 characters.", groups: ["registration"]
            )]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message:"You must enter a last name.")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Minimum 2 characters",
        maxMessage: "Maximum 50 characters"
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message:"You must enter a first name.")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Minimum 2 characters",
        maxMessage: "Maximum 50 characters"
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/^$|^\d{10}$/',
        message: "Your phone number must be composed of 10 digits"
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePic = null;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'eventPlanner', cascade: ['remove'])]
    private Collection $events;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(
        min:2,
        minMessage:"Minimum 2 characters",
        max:50,
        maxMessage:"Maximum 50 characters"
    )]
    #[Assert\NotNull(message:"Can't be null ! You must enter a unique nickname.")]
    private ?string $nickname = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'owner')]
    private Collection $ownedGroups;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'members')]
    private Collection $userGroupMemberships;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $registrationToken = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->ownedGroups = new ArrayCollection();
        $this->userGroupMemberships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    public function setProfilePic(?string $profilePic): static
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setEventPlanner($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getEventPlanner() === $this) {
                $event->setEventPlanner(null);
            }
        }

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

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


    public function isAdmin():bool
    {
        return \in_array('ROLE_ADMIN', $this->getRoles(), true);

    }
    /**
     * @return Collection<int, Group>
     */
    public function getOwnedGroups(): Collection
    {
        return $this->ownedGroups;
    }

    public function addOwnedGroup(Group $ownedGroup): static
    {
        if (!$this->ownedGroups->contains($ownedGroup)) {
            $this->ownedGroups->add($ownedGroup);
            $ownedGroup->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedGroup(Group $ownedGroup): static
    {
        if ($this->ownedGroups->removeElement($ownedGroup)) {
            // set the owning side to null (unless already changed)
            if ($ownedGroup->getOwner() === $this) {
                $ownedGroup->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getUserGroupMemberships(): Collection
    {
        return $this->userGroupMemberships;
    }

    public function addUserGroupMembership(Group $userGroupMembership): static
    {
        if (!$this->userGroupMemberships->contains($userGroupMembership)) {
            $this->userGroupMemberships->add($userGroupMembership);
            $userGroupMembership->addMember($this);
        }

        return $this;
    }

    public function removeUserGroupMembership(Group $userGroupMembership): static
    {
        if ($this->userGroupMemberships->removeElement($userGroupMembership)) {
            $userGroupMembership->removeMember($this);
        }

        return $this;

    }

    public function getRegistrationToken(): ?string
    {
        return $this->registrationToken;
    }

    public function setRegistrationToken(?string $registrationToken): static
    {
        $this->registrationToken = $registrationToken;

        return $this;
    }
}
