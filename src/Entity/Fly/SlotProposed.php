<?php

namespace App\Entity\Fly;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\IdTrait;
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
    #[ORM\JoinColumn(name: 'fly_type', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: FlyType::class)]
    #[OA\Property(
        description: 'Fly Type Identifier',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private FlyType $flyType;

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

    #[JMS\VirtualProperty]
    #[JMS\SerializedName('id')]
    #[JMS\Expose]
    public function serializedId(): ?int
    {
        return $this->getId();
    }

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\SerializedName('flyLocation')]
    public function getFlyLocation(): ?FlyLocation
    {
        return $this->getFlyType()->getFlyLocation();
    }

    public function getFlyType(): FlyType
    {
        return $this->flyType;
    }

    public function setFlyType(FlyType $flyType): self
    {
        $this->flyType = $flyType;
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
}
