<?php

namespace App\Entity\User;

use App\Entity\Interfaces\Serializable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[Embeddable]
class Address implements Serializable
{
    #[Assert\NotBlank]
    #[Column(type: "string", nullable: true)]
    private ?string $street;

    #[Assert\NotBlank]
    #[Column(type: "string", nullable: true)]
    private ?string $postalCode;

    #[Assert\NotBlank]
    #[Column(type: "string", nullable: true)]
    private ?string $city;

    #[Assert\NotBlank]
    #[Column(type: "string", nullable: true)]
    #[OA\Property(description: "ISO 3166-1 alpha-2", type: 'string', example: "FR")]
    private ?string $country;

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }
}
