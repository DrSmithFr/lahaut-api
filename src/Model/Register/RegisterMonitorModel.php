<?php

declare(strict_types=1);

namespace App\Model\Register;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterMonitorModel extends RegisterCustomerModel
{
    #[Assert\NotBlank]
    #[OA\Property(description: 'Firstname of user', type: 'string', example: 'john.doe@mail.com')]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[OA\Property(description: 'Lastname of user', type: 'string')]
    private ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^(\+33|0)[1-9](\d{2}){4}$/')]
    #[OA\Property(description: 'Phone of user', type: 'string', example: '+33612345678')]
    private ?string $phone = null;

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }
}
