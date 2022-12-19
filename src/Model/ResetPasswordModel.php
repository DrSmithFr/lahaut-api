<?php

declare(strict_types=1);

namespace App\Model;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordModel
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[OA\Property(description: 'Reset token of user', type: 'string', example: 'user@gmail.com')]
    private ?string $token = null;

    #[Assert\Length(min: 4)]
    #[OA\Property(description: 'Plaintext password', type: 'string', example: 'user-passwd')]
    private ?string $password = null;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
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
