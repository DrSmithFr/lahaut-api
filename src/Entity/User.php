<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\UserRepository;
use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use App\Enum\SecurityRoleEnum;
use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\Traits\BlamableTrait;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[JMS\ExclusionPolicy('all')]
class User implements UserInterface,
    PasswordAuthenticatedUserInterface,
    PasswordHasherAwareInterface
{
    use IdTrait;
    use TimestampableTrait;
    use BlamableTrait;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[JMS\Type('string')]
    #[JMS\Expose]
    #[JMS\Groups(['Default', 'Login'])]
    private ?UuidInterface $uuid = null;

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

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getPasswordHasherName(): ?string
    {
        return "harsh";
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string|null The username
     */
    public function getUsername(): ?string
    {
        return $this->getUuid() ? $this->getUuid()->toString() : null;
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
        if (!SecurityRoleEnum::isValidValue($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (!SecurityRoleEnum::isValidValue($role)) {
            throw new InvalidArgumentException('invalid role');
        }

        if (($key = array_search($role, $this->roles, true)) !== false) {
            array_splice($this->roles, $key, 1);
        }

        return $this;
    }

    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;
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
}
