<?php

namespace App\Entity\Activity;

use App\Entity\Activity\Place\LandingPoint;
use App\Entity\Activity\Place\MeetingPoint;
use App\Entity\Activity\Place\TakeOffPoint;
use App\Entity\_Interfaces\Serializable;
use App\Entity\Traits\UuidTrait;
use App\Repository\Activity\ActivityLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: ActivityLocationRepository::class)]
class ActivityLocation implements Serializable
{
    use UuidTrait;

    #[JMS\Expose]
    #[ORM\Column(unique: true, nullable: false)]
    private ?string $identifier;

    #[JMS\Expose]
    #[ORM\Column(nullable: false)]
    private ?string $name;

    #[JMS\Expose]
    #[JMS\Groups(['details'])]
    #[ORM\JoinColumn(name: 'take_off', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: TakeOffPoint::class)]
    #[OA\Property(
        description: 'Take Off Point Identifier',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private ?TakeOffPoint $takeOffPoint = null;

    #[JMS\Expose]
    #[JMS\Groups(['details'])]
    #[ORM\JoinColumn(name: 'meeting', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: MeetingPoint::class)]
    #[OA\Property(
        description: 'Meeting Point Identifier',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private ?MeetingPoint $meetingPoint = null;

    #[JMS\Expose]
    #[JMS\Groups(['details'])]
    #[ORM\JoinColumn(name: 'landing', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: LandingPoint::class)]
    #[OA\Property(
        description: 'Landing Point Identifier',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private ?LandingPoint $landingPoint = null;

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

    public function getTakeOffPoint(): ?TakeOffPoint
    {
        return $this->takeOffPoint;
    }

    public function setTakeOffPoint(?TakeOffPoint $takeOffPoint): self
    {
        $this->takeOffPoint = $takeOffPoint;
        return $this;
    }

    public function getMeetingPoint(): ?MeetingPoint
    {
        return $this->meetingPoint;
    }

    public function setMeetingPoint(?MeetingPoint $takeOff): self
    {
        $this->meetingPoint = $takeOff;

        return $this;
    }

    public function getLandingPoint(): ?LandingPoint
    {
        return $this->landingPoint;
    }

    public function setLandingPoint(?LandingPoint $landingPoint): self
    {
        $this->landingPoint = $landingPoint;
        return $this;
    }
}
