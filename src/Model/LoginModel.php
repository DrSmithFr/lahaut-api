<?php

declare(strict_types=1);

namespace App\Model;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class LoginModel
{
    #[Assert\Email]
    #[OA\Property(type: 'string', description: 'UUID of user', example: '30ae2d44-087e-4ea4-8048-e1cbb2bc1016')]
    private ?string $uuid = null;

    #[Assert\Length(min: 4)]
    #[OA\Property(type: 'string', description: 'Plaintext password')]
    private ?string $password = null;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
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
}
