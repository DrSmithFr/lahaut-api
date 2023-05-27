<?php

namespace App\Entity;

use App\Entity\Interfaces\Serializable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

#[Embeddable]
class Address implements Serializable
{
    #[JMS\Expose]
    #[Assert\NotBlank]
    #[Column(nullable: true)]
    private ?string $street;

    #[JMS\Expose]
    #[Assert\NotBlank]
    #[Column(nullable: true)]
    private ?string $zipCode;

    #[JMS\Expose]
    #[Assert\NotBlank]
    #[Column(nullable: true)]
    private ?string $city;

    #[JMS\Expose]
    #[Assert\NotBlank]
    #[Column(nullable: true)]
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

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;
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
