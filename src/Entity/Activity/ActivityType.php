<?php

namespace App\Entity\Activity;

use App\Entity\_Interfaces\Serializable;
use App\Entity\Traits\UuidTrait;
use App\Repository\Activity\ActivityTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: ActivityTypeRepository::class)]
class ActivityType implements Serializable
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
    #[ORM\JoinColumn(name: 'activity_location', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: ActivityLocation::class)]
    #[OA\Property(
        description: 'Activity Activity Identifier',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private ActivityLocation $activityLocation;

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

    public function getActivityLocation(): ActivityLocation
    {
        return $this->activityLocation;
    }

    public function setActivityLocation(ActivityLocation $activityLocation): self
    {
        $this->activityLocation = $activityLocation;
        return $this;
    }
}
