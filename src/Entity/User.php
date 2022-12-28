<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\EnableTrait;
use App\Entity\Traits\UuidTrait;
use App\Entity\User\Address;
use App\Entity\User\Identity;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use DateTime;
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
    #[ORM\Column(type: 'string', unique: true)]
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
    #[ORM\Column(name: 'roles', type: 'json')]
    #[JMS\Type('array<string>')]
    #[OA\Property(
        type: 'string[]',
        example: '["ROLE_CUSTOMER", "ROLE_MONITOR", "ROLE_ADMIN"]',
    )]
    private array $roles = [];

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $passwordResetTokenValidUntil = null;

    #[JMS\Expose]
    #[Embedded(class: Identity::class)]
    private Identity $identity;

    #[Embedded(class: Address::class, columnPrefix: "address_")]
    private Address $address;

    #[Embedded(class: Address::class, columnPrefix: "billing_")]
    private Address $billing;

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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole(string $role): self
    {
        if (!RoleEnum::tryFrom($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (!RoleEnum::tryFrom($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        $key = array_search($role, $this->roles, true);

        if ($key !== false) {
            array_splice($this->roles, $key, 1);
        }

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
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
}
