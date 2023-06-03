<?php

namespace App\Entity\Fly;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\IdTrait;
use App\Enum\FlyTypeEnum;
use App\Repository\Fly\SlotRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: SlotRepository::class)]
class SlotProposed implements Serializable
{
    use IdTrait;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'fly_location_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: FlyLocation::class)]
    #[OA\Property(
        description: 'Fly Location Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private FlyLocation $flyLocation;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'H:i'>")]
    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[OA\Property(
        description: 'Start at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $startAt;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'H:i'>")]
    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[OA\Property(
        description: 'End at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $endAt;

    #[JMS\Expose]
    #[JMS\Type("DateInterval<'PT%iM'>")]
    #[ORM\Column(type: Types::DATEINTERVAL)]
    #[OA\Property(
        description: 'Average fly duration',
        type: 'string',
        example: 'PT1M'
    )]
    private DateInterval $averageFlyDuration;

    #[JMS\Expose]
    #[JMS\Type(FlyTypeEnum::class)]
    #[ORM\Column(type: Types::STRING, enumType: FlyTypeEnum::class)]
    #[OA\Property(
        description: 'Fly type',
        type: 'string',
        example: 'discovery|freestyle|kid'
    )]
    private FlyTypeEnum $type;

    #[JMS\VirtualProperty]
    #[JMS\SerializedName('id')]
    #[JMS\Expose]
    public function serializedId(): ?int
    {
        return $this->getId();
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

    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getAverageFlyDuration(): DateInterval
    {
        return $this->averageFlyDuration;
    }

    public function setAverageFlyDuration(DateInterval $averageFlyDuration): self
    {
        $this->averageFlyDuration = $averageFlyDuration;
        return $this;
    }

    public function getType(): FlyTypeEnum
    {
        return $this->type;
    }

    public function setType(FlyTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }
}