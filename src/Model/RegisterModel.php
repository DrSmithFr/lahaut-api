<?php

declare(strict_types=1);

namespace App\Model;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterModel
{
    #[Assert\Email]
    #[OA\Property(type: 'string', description: 'Email of user', example: 'john.doe@gmail.com')]
    private ?string $email = null;

    #[Assert\Length(min: 4)]
    #[OA\Property(type: 'string', description: 'Plaintext password')]
    private ?string $password = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
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
