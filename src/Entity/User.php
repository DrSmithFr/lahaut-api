<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\_Interfaces\Serializable;
use App\Entity\_Utils\Address;
use App\Entity\Booking\Booking;
use App\Entity\Chat\Message;
use App\Entity\Slot\Slot;
use App\Entity\Traits\EnableTrait;
use App\Entity\Traits\UuidTrait;
use App\Entity\User\Identity;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[JMS\ExclusionPolicy('all')]
class User implements
    UserInterface,
    PasswordAuthenticatedUserInterface,
    PasswordHasherAwareInterface,
    Serializable
{
    use UuidTrait;

    use EnableTrait;
    use TimestampableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;

    #[Assert\Email]
    #[JMS\Type('string')]
    #[ORM\Column(unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    #[JMS\Groups(['Default', 'Login'])]
    private ?string $password = null;

    /**
     * Used internally for login form
     */
    private ?string $plainPassword = null;

    #[ORM\Column(nullable: true)]
    private ?string $salt = null;

    #[JMS\Expose]
    #[JMS\MaxDepth(1)]
    #[ORM\Column(name: 'roles', type: Types::SIMPLE_ARRAY)]
    #[JMS\Type('array<string>')]
    #[OA\Property(
        type: 'string[]',
        example: '["ROLE_CUSTOMER", "ROLE_MONITOR", "ROLE_ADMIN"]',
    )]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $passwordResetTokenValidUntil = null;

    #[JMS\Expose]
    #[Embedded(class: Identity::class)]
    private Identity $identity;

    #[Embedded(class: Address::class, columnPrefix: "address_")]
    private Address $address;

    #[Embedded(class: Address::class, columnPrefix: "billing_")]
    private Address $billing;

    #[JMS\Exclude]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: Message::class,
        cascade: ['remove'],
        fetch: 'EXTRA_LAZY'
    )]
    private Collection $messages;

    #[JMS\Exclude]
    #[ORM\OneToMany(
        mappedBy: 'monitor',
        targetEntity: Slot::class,
        cascade: ['remove'],
        fetch: 'EXTRA_LAZY'
    )]
    private Collection $slots;

    #[JMS\Exclude]
    #[ORM\OneToMany(
        mappedBy: 'customer',
        targetEntity: Booking::class,
        cascade: ['remove'],
        fetch: 'EXTRA_LAZY'
    )]
    private Collection $bookings;

    public function __construct()
    {
        $this->identity = new Identity();
        $this->address = new Address();
        $this->billing = new Address();
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getPasswordHasherName(): ?string
    {
        return "harsh";
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        $this->setPlainPassword(null);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        // ensure email is lowercase
        $this->email = strtolower($email);
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[]|RoleEnum[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string|RoleEnum $role): self
    {
        if (is_string($role) && !RoleEnum::tryFrom($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if ($role instanceof RoleEnum) {
            $role = $role->value;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string|RoleEnum $role): self
    {
        if (is_string($role) && !RoleEnum::tryFrom($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if ($role instanceof RoleEnum) {
            $role = $role->value;
        }

        $key = array_search($role, $this->roles, true);

        if ($key !== false) {
            array_splice($this->roles, $key, 1);
        }

        return $this;
    }

    public function hasRole(string|RoleEnum $role): bool
    {
        if (is_string($role) && !RoleEnum::tryFrom($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if ($role instanceof RoleEnum) {
            $role = $role->value;
        }

        return in_array($role, $this->roles, true);
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): self
    {
        $this->passwordResetToken = $passwordResetToken;
        return $this;
    }

    public function getPasswordResetTokenValidUntil(): ?DateTime
    {
        return $this->passwordResetTokenValidUntil;
    }

    public function setPasswordResetTokenValidUntil(?DateTime $passwordResetTokenValidUntil): self
    {
        $this->passwordResetTokenValidUntil = $passwordResetTokenValidUntil;
        return $this;
    }

    public function getIdentity(): Identity
    {
        return $this->identity;
    }

    public function setIdentity(Identity $identity): self
    {
        $this->identity = $identity;
        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getBilling(): Address
    {
        return $this->billing;
    }

    public function setBilling(Address $billing): self
    {
        $this->billing = $billing;
        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function setMessages(Collection $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    public function addMessage(Message $message): self
    {
        $this->messages->add($message);
        $message->setUser($this);
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        $this->messages->removeElement($message);
        $message->setUser(null);
        return $this;
    }

    public function getSlots(): Collection
    {
        return $this->slots;
    }

    public function setSlots(Collection $slots): self
    {
        $this->slots = $slots;
        return $this;
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function setBookings(Collection $bookings): self
    {
        $this->bookings = $bookings;
        return $this;
    }

    public function __toString(): string
    {
        return $this->getEmail();
    }
}
