<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\BlamableTrait;
use App\Entity\Traits\EnableTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\SecurityRoleEnum;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JMS\Serializer\Annotation as JMS;
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
    use IdTrait;
    use TimestampableTrait;
    use BlamableTrait;
    use EnableTrait;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\Email]
    #[JMS\Expose]
    #[JMS\Type('string')]
    #[JMS\Groups(['Default', 'Login'])]
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

    #[ORM\Column(name: 'roles', type: 'json')]
    #[JMS\Expose]
    #[JMS\Groups(['Default'])]
    #[JMS\MaxDepth(1)]
    #[JMS\Type('array<string>')]
    private array $roles = [];

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $passwordResetTokenValidUntil = null;

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

    public function addRole(string $role): self
    {
        if (!SecurityRoleEnum::tryFrom($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (!SecurityRoleEnum::tryFrom($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        $key = array_search($role, $this->roles, true);

        if ($key !== false) {
            array_splice($this->roles, $key, 1);
        }

        return $this;
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

    /**
     * @return string|null
     */
    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    /**
     * @param string|null $passwordResetToken
     */
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
}
