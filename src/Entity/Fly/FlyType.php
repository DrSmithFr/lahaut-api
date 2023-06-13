<?php

namespace App\Entity\Fly;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\UuidTrait;
use App\Repository\Fly\FlyTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: FlyTypeRepository::class)]
class FlyType implements Serializable
{
    use UuidTrait;

    #[JMS\Expose]
    #[ORM\Column(unique: true, nullable: false)]
    private ?string $identifier;

    #[JMS\Expose]
    #[ORM\Column(nullable: false)]
    private ?string $name;

    #[JMS\Expose]
    #[JMS\Groups(groups: ['location'])]
    #[ORM\JoinColumn(name: 'fly_location', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: FlyLocation::class)]
    #[OA\Property(
        description: 'Fly Location Identifier',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private FlyLocation $flyLocation;

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

    public function getFlyLocation(): FlyLocation
    {
        return $this->flyLocation;
    }

    public function setFlyLocation(FlyLocation $flyLocation): self
    {
        $this->flyLocation = $flyLocation;
        return $this;
    }
}
