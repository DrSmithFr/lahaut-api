<?php

namespace App\Entity\Fly\Place;

use App\Entity\Address;
use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\UuidTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\InheritanceType;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'type', type: 'string')]
#[DiscriminatorMap([
    'meeting' => MeetingPoint::class,
    'take_off' => TakeOffPoint::class,
    'landing' => LandingPoint::class,
])]
abstract class PlacePoint implements Serializable
{
    use UuidTrait;

    #[ORM\Column(unique: true, nullable: false)]
    private ?string $identifier;

    #[JMS\Expose]
    #[ORM\Column(nullable: false)]
    private ?string $name;

    #[JMS\Expose]
    #[JMS\Type('float')]
    #[ORM\Column(nullable: false)]
    private ?string $latitude;

    #[JMS\Expose]
    #[JMS\Type('float')]
    #[ORM\Column(nullable: false)]
    private ?string $longitude;

    #[JMS\Expose]
    #[Embedded(class: Address::class, columnPrefix: "address_")]
    private Address $address;

    #[JMS\Expose]
    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    public function __construct()
    {
        $this->address = new Address();
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
